<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Colaborador;
use App\Support\PlanillaCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class PlanillaController extends Controller
{
    public function index(Request $request): View
    {
        $editId = $request->filled('edit') ? (int) $request->input('edit') : null;
        $editingColaborador = $editId ? Colaborador::find($editId) : null;

        $colaboradores = Colaborador::query()
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->paginate(12)
            ->withQueryString();

        $resumen = [
            'total' => Colaborador::count(),
            'planilla' => Colaborador::where('tipo_pago', 'planilla')->count(),
            'honorarios' => Colaborador::where('tipo_pago', 'recibo_honorarios')->count(),
            'monto_total' => Colaborador::sum('honorarios'),
        ];

        return view('admin.contabilidad.planilla.index', [
            'colaboradores' => $colaboradores,
            'editingColaborador' => $editingColaborador,
            'tiposPago' => PlanillaCatalog::TIPOS_PAGO,
            'departamentos' => PlanillaCatalog::departamentos(),
            'subdepartamentosPorDepartamento' => PlanillaCatalog::subdepartamentosPorDepartamento(),
            'areasPorJerarquia' => PlanillaCatalog::areasPorJerarquia(),
            'resumen' => $resumen,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePayload($request);
        $newPaths = [];

        try {
            DB::transaction(function () use ($request, $data, &$newPaths) {
                $foto = $this->storeUploadedFile($request->file('foto'), 'planilla/fotos');
                $contrato = $this->storeUploadedFile($request->file('contrato'), 'planilla/contratos');

                $newPaths = array_values(array_filter([
                    $foto['path'] ?? null,
                    $contrato['path'] ?? null,
                ]));

                Colaborador::create([
                    ...$data,
                    'foto_path' => $foto['path'] ?? null,
                    'foto_original_name' => $foto['original_name'] ?? null,
                    'contrato_path' => $contrato['path'] ?? null,
                    'contrato_original_name' => $contrato['original_name'] ?? null,
                ]);
            });
        } catch (Throwable $exception) {
            if ($newPaths !== []) {
                Storage::disk('public')->delete($newPaths);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.contabilidad.planilla')
            ->with('success', 'Colaborador registrado correctamente.');
    }

    public function update(Request $request, Colaborador $colaborador): RedirectResponse
    {
        $data = $this->validatePayload($request, $colaborador);
        $newPaths = [];
        $oldPaths = [];

        try {
            DB::transaction(function () use ($request, $data, $colaborador, &$newPaths, &$oldPaths) {
                $foto = null;
                $contrato = null;

                if ($request->hasFile('foto')) {
                    $foto = $this->storeUploadedFile($request->file('foto'), 'planilla/fotos');
                    $newPaths[] = $foto['path'];

                    if ($colaborador->foto_path) {
                        $oldPaths[] = $colaborador->foto_path;
                    }
                }

                if ($request->hasFile('contrato')) {
                    $contrato = $this->storeUploadedFile($request->file('contrato'), 'planilla/contratos');
                    $newPaths[] = $contrato['path'];

                    if ($colaborador->contrato_path) {
                        $oldPaths[] = $colaborador->contrato_path;
                    }
                }

                $colaborador->update([
                    ...$data,
                    'foto_path' => $foto['path'] ?? $colaborador->foto_path,
                    'foto_original_name' => $foto['original_name'] ?? $colaborador->foto_original_name,
                    'contrato_path' => $contrato['path'] ?? $colaborador->contrato_path,
                    'contrato_original_name' => $contrato['original_name'] ?? $colaborador->contrato_original_name,
                ]);

                DB::afterCommit(function () use ($oldPaths) {
                    if ($oldPaths !== []) {
                        Storage::disk('public')->delete($oldPaths);
                    }
                });
            });
        } catch (Throwable $exception) {
            if ($newPaths !== []) {
                Storage::disk('public')->delete($newPaths);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.contabilidad.planilla')
            ->with('success', 'Colaborador actualizado correctamente.');
    }

    public function destroy(Colaborador $colaborador): RedirectResponse
    {
        $paths = array_values(array_filter([
            $colaborador->foto_path,
            $colaborador->contrato_path,
        ]));

        DB::transaction(function () use ($colaborador, $paths) {
            $colaborador->delete();

            DB::afterCommit(function () use ($paths) {
                if ($paths !== []) {
                    Storage::disk('public')->delete($paths);
                }
            });
        });

        return redirect()
            ->route('admin.contabilidad.planilla')
            ->with('success', 'Colaborador eliminado correctamente.');
    }

    public function contrato(Colaborador $colaborador)
    {
        abort_if(blank($colaborador->contrato_path), 404, 'El colaborador no tiene contrato adjunto.');
        abort_unless(Storage::disk('public')->exists($colaborador->contrato_path), 404, 'No se encontro el archivo solicitado.');

        return Storage::disk('public')->response(
            $colaborador->contrato_path,
            $colaborador->contrato_original_name ?: 'contrato'
        );
    }

    protected function validatePayload(Request $request, ?Colaborador $colaborador = null): array
    {
        return validator([
            'nombre' => trim((string) $request->input('nombre')),
            'apellido' => trim((string) $request->input('apellido')),
            'cargo' => trim((string) $request->input('cargo')),
            'celular' => trim((string) $request->input('celular')),
            'dni' => trim((string) $request->input('dni')),
            'redes_sociales' => $request->filled('redes_sociales') ? trim((string) $request->input('redes_sociales')) : null,
            'departamento' => $request->input('departamento'),
            'subdepartamento' => $request->input('subdepartamento'),
            'area' => $request->input('area'),
            'honorarios' => $request->input('honorarios'),
            'fecha_pago' => trim((string) $request->input('fecha_pago')),
            'tipo_pago' => $request->input('tipo_pago'),
            'foto' => $request->file('foto'),
            'contrato' => $request->file('contrato'),
        ], [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:120'],
            'cargo' => ['required', 'string', 'max:120'],
            'celular' => ['required', 'string', 'max:30'],
            'dni' => [
                'required',
                'string',
                'max:20',
                Rule::unique('colaboradores', 'dni')->ignore($colaborador?->id),
            ],
            'redes_sociales' => ['nullable', 'string', 'max:191'],
            'departamento' => ['required', Rule::in(PlanillaCatalog::departamentos())],
            'subdepartamento' => ['required', 'string', 'max:120'],
            'area' => ['required', 'string', 'max:120'],
            'honorarios' => ['required', 'numeric', 'min:0'],
            'fecha_pago' => ['required', 'string', 'max:100'],
            'tipo_pago' => ['required', Rule::in(array_keys(PlanillaCatalog::TIPOS_PAGO))],
            'foto' => ['nullable', 'file', 'max:5120', 'mimes:' . implode(',', PlanillaCatalog::FOTO_EXTENSIONES)],
            'contrato' => ['nullable', 'file', 'max:10240', 'mimes:' . implode(',', PlanillaCatalog::CONTRATO_EXTENSIONES)],
        ], [], [
            'redes_sociales' => 'redes sociales',
            'subdepartamento' => 'subdepartamento',
            'fecha_pago' => 'fecha de pago',
            'tipo_pago' => 'tipo de pago',
        ])->after(function ($validator) use ($request) {
            $departamento = $request->input('departamento');
            $subdepartamento = $request->input('subdepartamento');
            $area = $request->input('area');

            if (! PlanillaCatalog::isValidHierarchy($departamento, $subdepartamento, $area)) {
                $validator->errors()->add('area', 'La combinacion de departamento, subdepartamento y area no es valida.');
            }
        })->validate();
    }

    protected function storeUploadedFile($file, string $directory): ?array
    {
        if (! $file) {
            return null;
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $storedName = now()->format('YmdHis') . '_' . Str::random(12) . ($extension !== '' ? ".{$extension}" : '');
        $path = $file->storeAs($directory, $storedName, 'public');

        return [
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
        ];
    }
}
