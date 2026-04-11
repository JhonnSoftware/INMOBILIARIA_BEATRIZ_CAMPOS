<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use App\Support\ProveedorCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ProveedorController extends Controller
{
    public function index(Request $request): View
    {
        $editId = $request->filled('edit') ? (int) $request->input('edit') : null;
        $editingProveedor = $editId ? Proveedor::find($editId) : null;

        $proveedores = Proveedor::query()
            ->orderBy('empresa')
            ->paginate(12)
            ->withQueryString();

        $summary = [
            'pagos_vencidos' => Proveedor::query()
                ->whereNotNull('proximo_pago')
                ->whereDate('proximo_pago', '<', now()->toDateString())
                ->whereRaw('(monto_total - COALESCE(monto_pagado, 0)) > 0')
                ->count(),
            'proximos_vencer' => Proveedor::query()
                ->whereNotNull('proximo_pago')
                ->whereBetween('proximo_pago', [now()->toDateString(), now()->copy()->addDays(7)->toDateString()])
                ->whereRaw('(monto_total - COALESCE(monto_pagado, 0)) > 0')
                ->count(),
            'monto_total_general' => (float) Proveedor::sum('monto_total'),
            'total_pagado' => (float) Proveedor::sum('monto_pagado'),
            'total_pendiente' => (float) Proveedor::query()
                ->selectRaw('COALESCE(SUM(GREATEST(monto_total - COALESCE(monto_pagado, 0), 0)), 0) as total')
                ->value('total'),
            'con_yape_plin' => Proveedor::query()
                ->whereNotNull('yape_plin')
                ->where('yape_plin', '!=', '')
                ->count(),
            'con_cuenta_bancaria' => Proveedor::query()
                ->whereNotNull('cuenta_bancaria')
                ->where('cuenta_bancaria', '!=', '')
                ->count(),
        ];

        $categoryMap = $this->categoryMap($editingProveedor);
        $categoryCards = collect(array_keys($categoryMap))
            ->map(function (string $categoria) {
                return [
                    'categoria' => $categoria,
                    'total_proveedores' => 0,
                    'total_pendiente' => 0.0,
                    'style' => ProveedorCatalog::estilo($categoria),
                ];
            })
            ->keyBy('categoria');

        $realMetrics = Proveedor::query()
            ->selectRaw('categoria, COUNT(*) as total_proveedores, COALESCE(SUM(GREATEST(monto_total - COALESCE(monto_pagado, 0), 0)), 0) as total_pendiente')
            ->groupBy('categoria')
            ->orderByDesc('total_proveedores')
            ->get();

        foreach ($realMetrics as $row) {
            $categoryCards[$row->categoria] = [
                'categoria' => $row->categoria,
                'total_proveedores' => (int) $row->total_proveedores,
                'total_pendiente' => (float) $row->total_pendiente,
                'style' => ProveedorCatalog::estilo((string) $row->categoria),
            ];
        }

        return view('admin.contabilidad.proveedores.index', [
            'proveedores' => $proveedores,
            'editingProveedor' => $editingProveedor,
            'summary' => $summary,
            'categoryCards' => $categoryCards->values(),
            'categorias' => array_keys($categoryMap),
            'subcategoriasPorCategoria' => $categoryMap,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePayload($request);
        $newPath = null;

        try {
            DB::transaction(function () use ($data, $request, &$newPath) {
                $contrato = $this->storeContract($request->file('contrato'));
                $newPath = $contrato['path'] ?? null;

                Proveedor::create([
                    ...$data,
                    'contrato_path' => $contrato['path'] ?? null,
                    'contrato_original_name' => $contrato['original_name'] ?? null,
                ]);
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.contabilidad.proveedores')
            ->with('success', 'Proveedor registrado correctamente.');
    }

    public function update(Request $request, Proveedor $proveedor): RedirectResponse
    {
        $data = $this->validatePayload($request, $proveedor);
        $newPath = null;
        $oldPath = null;

        try {
            DB::transaction(function () use ($data, $request, $proveedor, &$newPath, &$oldPath) {
                $contrato = null;

                if ($request->hasFile('contrato')) {
                    $contrato = $this->storeContract($request->file('contrato'));
                    $newPath = $contrato['path'];
                    $oldPath = $proveedor->contrato_path;
                }

                $proveedor->update([
                    ...$data,
                    'contrato_path' => $contrato['path'] ?? $proveedor->contrato_path,
                    'contrato_original_name' => $contrato['original_name'] ?? $proveedor->contrato_original_name,
                ]);

                DB::afterCommit(function () use ($oldPath) {
                    if ($oldPath) {
                        Storage::disk('public')->delete($oldPath);
                    }
                });
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.contabilidad.proveedores')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        $path = $proveedor->contrato_path;

        DB::transaction(function () use ($proveedor, $path) {
            $proveedor->delete();

            DB::afterCommit(function () use ($path) {
                if ($path) {
                    Storage::disk('public')->delete($path);
                }
            });
        });

        return redirect()
            ->route('admin.contabilidad.proveedores')
            ->with('success', 'Proveedor eliminado correctamente.');
    }

    public function contrato(Proveedor $proveedor)
    {
        abort_if(blank($proveedor->contrato_path), 404, 'El proveedor no tiene contrato adjunto.');
        abort_unless(Storage::disk('public')->exists($proveedor->contrato_path), 404, 'No se encontro el archivo solicitado.');

        return Storage::disk('public')->response(
            $proveedor->contrato_path,
            $proveedor->contrato_original_name ?: 'contrato-proveedor'
        );
    }

    protected function validatePayload(Request $request, ?Proveedor $proveedor = null): array
    {
        $categoria = $this->resolveCatalogValue(
            trim((string) $request->input('categoria_select')),
            trim((string) $request->input('categoria_nueva'))
        );

        $subcategoria = $this->resolveCatalogValue(
            trim((string) $request->input('subcategoria_select')),
            trim((string) $request->input('subcategoria_nueva'))
        );

        return validator([
            'empresa' => trim((string) $request->input('empresa')),
            'ruc' => trim((string) $request->input('ruc')),
            'persona_contacto' => $request->filled('persona_contacto') ? trim((string) $request->input('persona_contacto')) : null,
            'telefono' => $request->filled('telefono') ? trim((string) $request->input('telefono')) : null,
            'departamento' => $request->filled('departamento') ? trim((string) $request->input('departamento')) : null,
            'provincia' => $request->filled('provincia') ? trim((string) $request->input('provincia')) : null,
            'distrito' => $request->filled('distrito') ? trim((string) $request->input('distrito')) : null,
            'email' => $request->filled('email') ? trim((string) $request->input('email')) : null,
            'categoria' => $categoria,
            'subcategoria' => $subcategoria,
            'descripcion_servicio' => $request->filled('descripcion_servicio') ? trim((string) $request->input('descripcion_servicio')) : null,
            'yape_plin' => $request->filled('yape_plin') ? trim((string) $request->input('yape_plin')) : null,
            'cuenta_bancaria' => $request->filled('cuenta_bancaria') ? trim((string) $request->input('cuenta_bancaria')) : null,
            'proximo_pago' => $request->filled('proximo_pago') ? $request->input('proximo_pago') : null,
            'monto_total' => $request->input('monto_total'),
            'monto_pagado' => $request->filled('monto_pagado') ? $request->input('monto_pagado') : 0,
            'contrato' => $request->file('contrato'),
        ], [
            'empresa' => ['required', 'string', 'max:191'],
            'ruc' => [
                'required',
                'string',
                'max:20',
                Rule::unique('proveedores', 'ruc')->ignore($proveedor?->id),
            ],
            'persona_contacto' => ['nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:40'],
            'departamento' => ['nullable', 'string', 'max:120'],
            'provincia' => ['nullable', 'string', 'max:120'],
            'distrito' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:191'],
            'categoria' => ['required', 'string', 'max:80'],
            'subcategoria' => ['required', 'string', 'max:80'],
            'descripcion_servicio' => ['nullable', 'string', 'max:191'],
            'yape_plin' => ['nullable', 'string', 'max:80'],
            'cuenta_bancaria' => ['nullable', 'string', 'max:120'],
            'proximo_pago' => ['nullable', 'date'],
            'monto_total' => ['required', 'numeric', 'min:0'],
            'monto_pagado' => ['nullable', 'numeric', 'min:0'],
            'contrato' => ['nullable', 'file', 'max:10240', 'mimes:' . implode(',', ProveedorCatalog::CONTRATO_EXTENSIONES)],
        ], [], [
            'persona_contacto' => 'persona de contacto',
            'descripcion_servicio' => 'descripcion del servicio',
            'yape_plin' => 'numero YAPE/PLIN',
            'cuenta_bancaria' => 'numero de cuenta bancaria',
            'proximo_pago' => 'fecha proximo pago',
            'monto_total' => 'monto total',
            'monto_pagado' => 'monto pagado',
        ])->after(function ($validator) use ($request) {
            $montoTotal = (float) $request->input('monto_total', 0);
            $montoPagado = (float) $request->input('monto_pagado', 0);

            if ($montoPagado > $montoTotal) {
                $validator->errors()->add('monto_pagado', 'El monto pagado no puede superar el monto total.');
            }
        })->validate();
    }

    protected function resolveCatalogValue(string $selected, string $custom): ?string
    {
        if ($custom !== '') {
            return $custom;
        }

        return $selected !== '' ? $selected : null;
    }

    protected function storeContract($file): ?array
    {
        if (! $file) {
            return null;
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $storedName = now()->format('YmdHis') . '_' . Str::random(12) . ($extension !== '' ? ".{$extension}" : '');
        $path = $file->storeAs('proveedores/contratos', $storedName, 'public');

        return [
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ];
    }

    protected function categoryMap(?Proveedor $editingProveedor = null): array
    {
        $map = ProveedorCatalog::categoriasConSubcategorias();

        Proveedor::query()
            ->select('categoria', 'subcategoria')
            ->whereNotNull('categoria')
            ->whereNotNull('subcategoria')
            ->get()
            ->groupBy('categoria')
            ->each(function ($rows, $categoria) use (&$map) {
                $existing = collect($map[$categoria] ?? [])
                    ->merge($rows->pluck('subcategoria')->filter()->all())
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $map[$categoria] = $existing;
            });

        if ($editingProveedor && filled($editingProveedor->categoria)) {
            $map[$editingProveedor->categoria] = collect($map[$editingProveedor->categoria] ?? [])
                ->merge(filled($editingProveedor->subcategoria) ? [$editingProveedor->subcategoria] : [])
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        ksort($map);

        return $map;
    }
}
