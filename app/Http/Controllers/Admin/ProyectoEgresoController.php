<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Egreso;
use App\Models\EgresoArchivo;
use App\Models\Proyecto;
use App\Support\EgresoCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ProyectoEgresoController extends Controller
{
    public function index(Request $request, Proyecto $proyecto): View
    {
        $buscar = trim((string) $request->string('buscar'));
        $fecha = $request->input('fecha');
        $mes = $request->filled('mes') ? max((int) $request->input('mes'), 1) : null;
        $mes = $mes && $mes <= 12 ? $mes : null;
        $anio = $request->filled('anio') ? max((int) $request->input('anio'), 2020) : null;
        $categoriaPrincipal = $request->string('categoria_principal')->toString();
        $categoriaPrincipal = in_array($categoriaPrincipal, EgresoCatalog::principales(), true) ? $categoriaPrincipal : null;
        $categoria = $request->string('categoria')->toString();
        $categoria = in_array($categoria, EgresoCatalog::subcategorias($categoriaPrincipal), true) ? $categoria : null;
        $responsable = trim((string) $request->string('responsable'));
        $estado = $request->string('estado')->toString();
        $estado = array_key_exists($estado, EgresoCatalog::ESTADOS) ? $estado : null;
        $fuente = $request->string('fuente_dinero')->toString();
        $fuente = array_key_exists($fuente, EgresoCatalog::FUENTES_DINERO) ? $fuente : null;

        $query = $proyecto->egresos()
            ->withCount('archivos')
            ->when($fecha, fn ($builder) => $builder->whereDate('fecha', $fecha))
            ->when($mes, fn ($builder) => $builder->whereMonth('fecha', $mes))
            ->when($anio, fn ($builder) => $builder->whereYear('fecha', $anio))
            ->when($categoriaPrincipal, fn ($builder) => $builder->where('categoria_principal', $categoriaPrincipal))
            ->when($categoria, fn ($builder) => $builder->where('categoria', $categoria))
            ->when($responsable !== '', fn ($builder) => $builder->where('responsable', 'like', "%{$responsable}%"))
            ->when($estado, fn ($builder) => $builder->where('estado', $estado))
            ->when($fuente, fn ($builder) => $builder->where('fuente_dinero', $fuente))
            ->when($buscar !== '', function ($builder) use ($buscar) {
                $builder->where(function ($inner) use ($buscar) {
                    $inner->where('descripcion', 'like', "%{$buscar}%")
                        ->orWhere('observaciones', 'like', "%{$buscar}%")
                        ->orWhere('responsable', 'like', "%{$buscar}%")
                        ->orWhere('categoria_principal', 'like', "%{$buscar}%")
                        ->orWhere('categoria', 'like', "%{$buscar}%")
                        ->orWhere('tipo_comprobante', 'like', "%{$buscar}%")
                        ->orWhere('serie_comprobante', 'like', "%{$buscar}%")
                        ->orWhere('numero_comprobante', 'like', "%{$buscar}%")
                        ->orWhere('ruc_proveedor', 'like', "%{$buscar}%")
                        ->orWhere('razon_social', 'like', "%{$buscar}%")
                        ->orWhere('tipo_compra', 'like', "%{$buscar}%");
                });
            });

        $egresos = (clone $query)
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $resumen = [
            'total_periodo' => (clone $query)->where('estado', 'registrado')->sum('monto'),
            'total_mes_actual' => $proyecto->egresos()
                ->where('estado', 'registrado')
                ->whereBetween('fecha', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                ->sum('monto'),
            'cantidad_registros' => (clone $query)->count(),
            'adjuntos' => (clone $query)->withCount('archivos')->get()->sum('archivos_count'),
        ];

        $totalesPorPrincipal = (clone $query)
            ->where('estado', 'registrado')
            ->select('categoria_principal', DB::raw('SUM(monto) as total'))
            ->groupBy('categoria_principal')
            ->orderByDesc('total')
            ->pluck('total', 'categoria_principal')
            ->all();

        $totalesPorCategoria = (clone $query)
            ->where('estado', 'registrado')
            ->select('categoria', DB::raw('SUM(monto) as total'))
            ->groupBy('categoria')
            ->orderByDesc('total')
            ->pluck('total', 'categoria')
            ->all();

        return view('admin.proyectos.egresos.index', [
            'proyecto' => $proyecto,
            'egresos' => $egresos,
            'resumen' => $resumen,
            'totalesPorPrincipal' => $totalesPorPrincipal,
            'totalesPorCategoria' => $totalesPorCategoria,
            'buscar' => $buscar,
            'fecha' => $fecha,
            'mes' => $mes,
            'anio' => $anio,
            'categoriaPrincipal' => $categoriaPrincipal,
            'categoria' => $categoria,
            'responsable' => $responsable,
            'estado' => $estado,
            'fuente' => $fuente,
            'categoriasPrincipales' => EgresoCatalog::principales(),
            'categoriasPorPrincipal' => EgresoCatalog::categoriasPorPrincipal(),
            'categoriasDisponibles' => EgresoCatalog::subcategorias($categoriaPrincipal),
            'fuentesDinero' => EgresoCatalog::FUENTES_DINERO,
            'estados' => EgresoCatalog::ESTADOS,
        ]);
    }

    public function create(Proyecto $proyecto): View
    {
        $categoriaPrincipal = EgresoCatalog::principales()[0];

        return view('admin.proyectos.egresos.create', [
            'proyecto' => $proyecto,
            'egreso' => new Egreso([
                'fecha' => now()->toDateString(),
                'categoria_principal' => $categoriaPrincipal,
                'categoria' => EgresoCatalog::subcategorias($categoriaPrincipal)[0] ?? 'Otros',
                'fuente_dinero' => 'caja_general',
                'estado' => 'registrado',
            ]),
            'categoriasPrincipales' => EgresoCatalog::principales(),
            'categoriasPorPrincipal' => EgresoCatalog::categoriasPorPrincipal(),
            'fuentesDinero' => EgresoCatalog::FUENTES_DINERO,
        ]);
    }

    public function store(Request $request, Proyecto $proyecto): RedirectResponse
    {
        $data = $this->validatePayload($request);
        $archivos = $request->file('archivos', []);
        $paths = [];

        try {
            DB::transaction(function () use ($request, $proyecto, $data, $archivos, &$paths) {
                $egreso = $proyecto->egresos()->create([
                    ...$data,
                    'estado' => 'registrado',
                    'creado_por' => $this->actorName($request),
                    'updated_by' => $this->actorName($request),
                ]);

                $paths = $this->storeArchivos($egreso, $archivos);
            });
        } catch (Throwable $exception) {
            if ($paths !== []) {
                Storage::disk('public')->delete($paths);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.proyectos.egresos', $proyecto)
            ->with('success', 'Egreso registrado correctamente.');
    }

    public function edit(Request $request, Proyecto $proyecto, Egreso $egreso): View
    {
        $this->authorizeManagement($request, $egreso);

        return view('admin.proyectos.egresos.edit', [
            'proyecto' => $proyecto,
            'egreso' => $egreso->load('archivos'),
            'categoriasPrincipales' => EgresoCatalog::principales(),
            'categoriasPorPrincipal' => EgresoCatalog::categoriasPorPrincipal(),
            'fuentesDinero' => EgresoCatalog::FUENTES_DINERO,
        ]);
    }

    public function update(Request $request, Proyecto $proyecto, Egreso $egreso): RedirectResponse
    {
        $this->authorizeManagement($request, $egreso);

        $data = $this->validatePayload($request);
        $archivos = $request->file('archivos', []);
        $paths = [];

        try {
            DB::transaction(function () use ($request, $egreso, $data, $archivos, &$paths) {
                $egreso->update([
                    ...$data,
                    'updated_by' => $this->actorName($request),
                ]);

                $paths = $this->storeArchivos($egreso, $archivos);
            });
        } catch (Throwable $exception) {
            if ($paths !== []) {
                Storage::disk('public')->delete($paths);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.proyectos.egresos', $proyecto)
            ->with('success', 'Egreso actualizado correctamente.');
    }

    public function destroy(Request $request, Proyecto $proyecto, Egreso $egreso): RedirectResponse
    {
        $this->authorizeManagement($request, $egreso);

        $rutas = $egreso->archivos()->pluck('ruta_archivo')->filter()->values()->all();

        DB::transaction(function () use ($egreso, $rutas) {
            $egreso->delete();

            DB::afterCommit(function () use ($rutas) {
                if ($rutas !== []) {
                    Storage::disk('public')->delete($rutas);
                }
            });
        });

        return redirect()
            ->route('admin.proyectos.egresos', $proyecto)
            ->with('success', 'Egreso eliminado correctamente.');
    }

    public function storeArchivo(Request $request, Proyecto $proyecto, Egreso $egreso): RedirectResponse
    {
        abort_unless((int) $egreso->proyecto_id === (int) $proyecto->id, 404);

        $request->validate([
            'archivos'   => ['required', 'array', 'min:1'],
            'archivos.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,gif,doc,docx,xls,xlsx,txt'],
        ], [], [
            'archivos'   => 'archivos',
            'archivos.*' => 'archivo adjunto',
        ]);

        $paths = [];

        try {
            DB::transaction(function () use ($egreso, $request, &$paths) {
                $paths = $this->storeArchivos($egreso, $request->file('archivos', []));
            });
        } catch (Throwable $exception) {
            if ($paths !== []) {
                Storage::disk('public')->delete($paths);
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.proyectos.egresos', $proyecto)
            ->with('success', count($paths) . ' archivo(s) subido(s) correctamente.');
    }

    public function destroyArchivo(Request $request, Proyecto $proyecto, Egreso $egreso, EgresoArchivo $archivo): RedirectResponse
    {
        $this->authorizeManagement($request, $egreso);
        abort_unless((int) $archivo->egreso_id === (int) $egreso->id, 404);

        $ruta = $archivo->ruta_archivo;

        DB::transaction(function () use ($archivo, $ruta) {
            $archivo->delete();

            DB::afterCommit(function () use ($ruta) {
                if ($ruta) {
                    Storage::disk('public')->delete($ruta);
                }
            });
        });

        return redirect()
            ->route('admin.proyectos.egresos.edit', [$proyecto, $egreso])
            ->with('success', 'Archivo adjunto eliminado correctamente.');
    }

    protected function validatePayload(Request $request): array
    {
        return validator([
            'fecha' => $request->input('fecha'),
            'categoria_principal' => $request->input('categoria_principal'),
            'categoria' => $request->input('categoria'),
            'monto' => $request->input('monto'),
            'descripcion' => $request->filled('descripcion') ? trim((string) $request->input('descripcion')) : null,
            'observaciones' => $request->filled('observaciones') ? trim((string) $request->input('observaciones')) : null,
            'responsable' => $request->filled('responsable') ? trim((string) $request->input('responsable')) : null,
            'fuente_dinero' => $request->input('fuente_dinero'),
            'tipo_comprobante' => $request->filled('tipo_comprobante') ? trim((string) $request->input('tipo_comprobante')) : null,
            'serie_comprobante' => $request->filled('serie_comprobante') ? trim((string) $request->input('serie_comprobante')) : null,
            'numero_comprobante' => $request->filled('numero_comprobante') ? trim((string) $request->input('numero_comprobante')) : null,
            'ruc_proveedor' => $request->filled('ruc_proveedor') ? trim((string) $request->input('ruc_proveedor')) : null,
            'razon_social' => $request->filled('razon_social') ? trim((string) $request->input('razon_social')) : null,
            'tipo_compra' => $request->filled('tipo_compra') ? trim((string) $request->input('tipo_compra')) : null,
            'detalles_proveedor' => $request->filled('detalles_proveedor') ? trim((string) $request->input('detalles_proveedor')) : null,
            'archivos' => $request->file('archivos', []),
        ], [
            'fecha' => ['required', 'date'],
            'categoria_principal' => ['required', 'string', Rule::in(EgresoCatalog::principales())],
            'categoria' => ['required', 'string', 'max:80'],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'descripcion' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            'responsable' => ['nullable', 'string', 'max:150'],
            'fuente_dinero' => ['required', Rule::in(array_keys(EgresoCatalog::FUENTES_DINERO))],
            'tipo_comprobante' => ['nullable', 'string', 'max:50'],
            'serie_comprobante' => ['nullable', 'string', 'max:30'],
            'numero_comprobante' => ['nullable', 'string', 'max:50'],
            'ruc_proveedor' => ['nullable', 'string', 'max:20'],
            'razon_social' => ['nullable', 'string', 'max:191'],
            'tipo_compra' => ['nullable', 'string', 'max:80'],
            'detalles_proveedor' => ['nullable', 'string'],
            'archivos' => ['nullable', 'array'],
            'archivos.*' => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,gif,doc,docx,xls,xlsx,txt'],
        ], [], [
            'categoria_principal' => 'categoria principal',
            'fuente_dinero' => 'fuente de dinero',
            'tipo_comprobante' => 'tipo de comprobante',
            'serie_comprobante' => 'serie del comprobante',
            'numero_comprobante' => 'numero del comprobante',
            'ruc_proveedor' => 'RUC del proveedor',
            'razon_social' => 'razon social',
            'tipo_compra' => 'tipo de compra',
            'detalles_proveedor' => 'detalles del proveedor',
            'archivos.*' => 'archivo adjunto',
        ])->after(function ($validator) use ($request) {
            if (! EgresoCatalog::isValidCategoria($request->input('categoria_principal'), $request->input('categoria'))) {
                $validator->errors()->add('categoria', 'La subcategoria seleccionada no corresponde a la categoria principal.');
            }
        })->validate();
    }

    protected function storeArchivos(Egreso $egreso, array $archivos): array
    {
        $paths = [];

        foreach ($archivos as $archivo) {
            if (! $archivo) {
                continue;
            }

            $extension = strtolower((string) $archivo->getClientOriginalExtension());
            $storedName = now()->format('YmdHis') . '_' . Str::random(12) . ($extension !== '' ? ".{$extension}" : '');
            $ruta = $archivo->storeAs("egresos/proyecto_{$egreso->proyecto_id}/egreso_{$egreso->id}", $storedName, 'public');

            $egreso->archivos()->create([
                'nombre_archivo' => $storedName,
                'nombre_original' => $archivo->getClientOriginalName(),
                'ruta_archivo' => $ruta,
                'tipo_archivo' => $archivo->getMimeType(),
                'tamano_archivo' => $archivo->getSize(),
            ]);

            $paths[] = $ruta;
        }

        return $paths;
    }

    protected function authorizeManagement(Request $request, Egreso $egreso): void
    {
        $user = $request->user();

        if (! $user || blank($egreso->creado_por)) {
            return;
        }

        abort_unless(
            strcasecmp((string) $egreso->creado_por, (string) $user->name) === 0,
            403,
            'No tienes permiso para modificar este egreso.'
        );
    }

    protected function actorName(Request $request): string
    {
        return $request->user()?->name ?? 'Administrador';
    }
}
