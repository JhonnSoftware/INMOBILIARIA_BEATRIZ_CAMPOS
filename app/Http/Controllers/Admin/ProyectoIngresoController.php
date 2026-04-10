<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingreso;
use App\Models\Proyecto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use InvalidArgumentException;

class ProyectoIngresoController extends Controller
{
    public function index(Request $request, Proyecto $proyecto): View
    {
        $buscar = trim((string) $request->input('buscar'));
        $fecha = $request->input('fecha');
        $desde = $request->input('desde');
        $hasta = $request->input('hasta');
        $tipo = $request->string('tipo_ingreso')->toString();
        $origen = $request->string('origen')->toString();
        $clienteId = $request->integer('cliente_id');
        $montoMin = $request->filled('monto_min') ? (float) $request->input('monto_min') : null;
        $montoMax = $request->filled('monto_max') ? (float) $request->input('monto_max') : null;

        $tipo = in_array($tipo, Ingreso::TIPOS, true) ? $tipo : null;
        $origen = in_array($origen, Ingreso::ORIGENES, true) ? $origen : null;

        $query = $proyecto->ingresos()
            ->with(['cliente', 'lote', 'pago'])
            ->when($fecha, fn ($builder) => $builder->whereDate('fecha_ingreso', $fecha))
            ->when($desde, fn ($builder) => $builder->whereDate('fecha_ingreso', '>=', $desde))
            ->when($hasta, fn ($builder) => $builder->whereDate('fecha_ingreso', '<=', $hasta))
            ->when($tipo, fn ($builder) => $builder->where('tipo_ingreso', $tipo))
            ->when($origen, fn ($builder) => $builder->where('origen', $origen))
            ->when($clienteId, fn ($builder) => $builder->where('cliente_id', $clienteId))
            ->when($montoMin !== null, fn ($builder) => $builder->where('monto', '>=', $montoMin))
            ->when($montoMax !== null, fn ($builder) => $builder->where('monto', '<=', $montoMax))
            ->when($buscar !== '', function ($builder) use ($buscar) {
                $builder->where(function ($inner) use ($buscar) {
                    $inner->where('concepto', 'like', "%{$buscar}%")
                        ->orWhere('descripcion', 'like', "%{$buscar}%")
                        ->orWhere('observaciones', 'like', "%{$buscar}%")
                        ->orWhereHas('cliente', function ($clienteQuery) use ($buscar) {
                            $clienteQuery->where('nombres', 'like', "%{$buscar}%")
                                ->orWhere('apellidos', 'like', "%{$buscar}%")
                                ->orWhere('dni', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('lote', function ($loteQuery) use ($buscar) {
                            $loteQuery->where('manzana', 'like', "%{$buscar}%")
                                ->orWhere('numero', 'like', "%{$buscar}%")
                                ->orWhere('codigo', 'like', "%{$buscar}%");
                        });
                });
            });

        $ingresos = (clone $query)
            ->orderByDesc('fecha_ingreso')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $resumen = [
            'periodo'  => (clone $query)->where('estado', 'registrado')->sum('monto'),
            'hoy'      => $proyecto->ingresos()->where('estado', 'registrado')->whereDate('fecha_ingreso', now()->toDateString())->sum('monto'),
            'mes'      => $proyecto->ingresos()->where('estado', 'registrado')->whereBetween('fecha_ingreso', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->sum('monto'),
            'cobranza' => (clone $query)->where('estado', 'registrado')->where('origen', 'cobranza')->sum('monto'),
            'manual'   => (clone $query)->where('estado', 'registrado')->where('origen', 'manual')->sum('monto'),
            'masAlto'  => $proyecto->ingresos()->where('estado', 'registrado')->max('monto') ?? 0,
            'cantidad' => (clone $query)->where('estado', 'registrado')->count(),
        ];

        // Ingresos por día del mes actual (para gráfica)
        $ingresosPorDia = $proyecto->ingresos()
            ->where('estado', 'registrado')
            ->whereBetween('fecha_ingreso', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->select('fecha_ingreso', DB::raw('SUM(monto) as total'))
            ->groupBy('fecha_ingreso')
            ->orderBy('fecha_ingreso')
            ->pluck('total', 'fecha_ingreso')
            ->all();

        // Ingresos por mes del año actual
        $ingresosPorMes = $proyecto->ingresos()
            ->where('estado', 'registrado')
            ->whereYear('fecha_ingreso', now()->year)
            ->select(DB::raw('MONTH(fecha_ingreso) as mes'), DB::raw('SUM(monto) as total'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes')
            ->all();

        // Últimos ingresos registrados
        $ultimosIngresos = $proyecto->ingresos()
            ->with(['cliente', 'lote'])
            ->where('estado', 'registrado')
            ->orderByDesc('fecha_ingreso')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('admin.proyectos.ingresos.index', [
            'proyecto'        => $proyecto,
            'ingresos'        => $ingresos,
            'resumen'         => $resumen,
            'ingresosPorDia'  => $ingresosPorDia,
            'ingresosPorMes'  => $ingresosPorMes,
            'ultimosIngresos' => $ultimosIngresos,
            'buscar'          => $buscar,
            'fecha'           => $fecha,
            'desde'           => $desde,
            'hasta'           => $hasta,
            'tipo'            => $tipo,
            'origen'          => $origen,
            'clienteId'       => $clienteId,
            'montoMin'        => $montoMin,
            'montoMax'        => $montoMax,
            'tipos'           => Ingreso::TIPOS,
            'origenes'        => Ingreso::ORIGENES,
            'clientes'        => $proyecto->clientes()->orderBy('apellidos')->orderBy('nombres')->get(),
        ]);
    }

    public function create(Proyecto $proyecto): View
    {
        return view('admin.proyectos.ingresos.create', [
            'proyecto' => $proyecto,
            'ingreso' => new Ingreso([
                'fecha_ingreso' => now()->toDateString(),
                'tipo_ingreso' => 'extra',
                'moneda' => 'PEN',
            ]),
            'tipos' => Ingreso::TIPOS,
            'clientes' => $proyecto->clientes()->with('lote')->orderBy('apellidos')->orderBy('nombres')->get(),
            'lotes' => $proyecto->lotes()->orderBy('manzana')->orderBy('numero')->get(),
        ]);
    }

    public function store(Request $request, Proyecto $proyecto): RedirectResponse
    {
        $data = $this->validatePayload($request, $proyecto);

        DB::transaction(function () use ($data, $proyecto, $request) {
            [$cliente, $lote] = $this->resolveRelations($proyecto, $data);

            $proyecto->ingresos()->create([
                'cliente_id' => $cliente?->id,
                'lote_id' => $lote?->id,
                'pago_id' => null,
                'fecha_ingreso' => $data['fecha_ingreso'],
                'concepto' => $data['concepto'],
                'tipo_ingreso' => $data['tipo_ingreso'],
                'origen' => 'manual',
                'monto' => round((float) $data['monto'], 2),
                'moneda' => 'PEN',
                'descripcion' => $data['descripcion'] ?? null,
                'observaciones' => $data['observaciones'] ?? null,
                'estado' => 'registrado',
                'registrado_por' => $request->user()?->name ?? 'Administrador',
            ]);
        });

        return redirect()
            ->route('admin.proyectos.ingresos', $proyecto)
            ->with('success', 'Ingreso manual registrado correctamente.');
    }

    public function edit(Proyecto $proyecto, Ingreso $ingreso): View
    {
        $this->ensureManual($ingreso);

        return view('admin.proyectos.ingresos.edit', [
            'proyecto' => $proyecto,
            'ingreso' => $ingreso->loadMissing(['cliente', 'lote']),
            'tipos' => Ingreso::TIPOS,
            'clientes' => $proyecto->clientes()->with('lote')->orderBy('apellidos')->orderBy('nombres')->get(),
            'lotes' => $proyecto->lotes()->orderBy('manzana')->orderBy('numero')->get(),
        ]);
    }

    public function update(Request $request, Proyecto $proyecto, Ingreso $ingreso): RedirectResponse
    {
        $this->ensureManual($ingreso);
        $data = $this->validatePayload($request, $proyecto, $ingreso);

        DB::transaction(function () use ($data, $ingreso, $request) {
            [$cliente, $lote] = $this->resolveRelations($ingreso->proyecto, $data);

            $ingreso->update([
                'cliente_id' => $cliente?->id,
                'lote_id' => $lote?->id,
                'fecha_ingreso' => $data['fecha_ingreso'],
                'concepto' => $data['concepto'],
                'tipo_ingreso' => $data['tipo_ingreso'],
                'monto' => round((float) $data['monto'], 2),
                'descripcion' => $data['descripcion'] ?? null,
                'observaciones' => $data['observaciones'] ?? null,
                'registrado_por' => $request->user()?->name ?? $ingreso->registrado_por,
            ]);
        });

        return redirect()
            ->route('admin.proyectos.ingresos', $proyecto)
            ->with('success', 'Ingreso manual actualizado correctamente.');
    }

    public function destroy(Proyecto $proyecto, Ingreso $ingreso): RedirectResponse
    {
        $this->ensureManual($ingreso);

        DB::transaction(function () use ($ingreso) {
            $ingreso->delete();
        });

        return redirect()
            ->route('admin.proyectos.ingresos', $proyecto)
            ->with('success', 'Ingreso manual eliminado correctamente.');
    }

    protected function validatePayload(Request $request, Proyecto $proyecto, ?Ingreso $ingreso = null): array
    {
        return validator([
            'fecha_ingreso' => $request->input('fecha_ingreso'),
            'concepto' => trim((string) $request->input('concepto')),
            'tipo_ingreso' => $request->input('tipo_ingreso'),
            'monto' => $request->input('monto'),
            'descripcion' => $request->filled('descripcion') ? trim((string) $request->input('descripcion')) : null,
            'observaciones' => $request->filled('observaciones') ? trim((string) $request->input('observaciones')) : null,
            'cliente_id' => $request->filled('cliente_id') ? $request->input('cliente_id') : null,
            'lote_id' => $request->filled('lote_id') ? $request->input('lote_id') : null,
        ], [
            'fecha_ingreso' => ['required', 'date'],
            'concepto' => ['required', 'string', 'max:150'],
            'tipo_ingreso' => ['required', Rule::in(Ingreso::TIPOS)],
            'monto' => ['required', 'numeric', 'min:0.01'],
            'descripcion' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            'cliente_id' => [
                'nullable',
                'integer',
                Rule::exists('clientes', 'id')->where(fn ($query) => $query->where('proyecto_id', $proyecto->id)),
            ],
            'lote_id' => [
                'nullable',
                'integer',
                Rule::exists('lotes', 'id')->where(fn ($query) => $query->where('proyecto_id', $proyecto->id)),
            ],
        ], [], [
            'fecha_ingreso' => 'fecha de ingreso',
            'tipo_ingreso' => 'tipo de ingreso',
            'cliente_id' => 'cliente',
            'lote_id' => 'lote',
        ])->after(function ($validator) use ($proyecto, $request) {
            $clienteId = $request->filled('cliente_id') ? (int) $request->input('cliente_id') : null;
            $loteId = $request->filled('lote_id') ? (int) $request->input('lote_id') : null;

            if (! $clienteId || ! $loteId) {
                return;
            }

            $cliente = $proyecto->clientes()->find($clienteId);

            if ($cliente && $cliente->lote_id && (int) $cliente->lote_id !== $loteId) {
                $validator->errors()->add('lote_id', 'El lote seleccionado no coincide con el cliente elegido.');
            }
        })->validate();
    }

    protected function resolveRelations(Proyecto $proyecto, array $data): array
    {
        $cliente = null;
        $lote = null;

        if (! empty($data['cliente_id'])) {
            $cliente = $proyecto->clientes()->with('lote')->findOrFail((int) $data['cliente_id']);
        }

        if (! empty($data['lote_id'])) {
            $lote = $proyecto->lotes()->findOrFail((int) $data['lote_id']);
        }

        if ($cliente && ! $lote && $cliente->lote_id) {
            $lote = $cliente->lote;
        }

        if ($cliente && $lote && (int) $cliente->lote_id !== (int) $lote->id) {
            throw new InvalidArgumentException('El lote seleccionado no coincide con el cliente elegido.');
        }

        return [$cliente, $lote];
    }

    protected function ensureManual(Ingreso $ingreso): void
    {
        abort_if($ingreso->origen !== 'manual', 403, 'Los ingresos generados desde cobranza son de solo lectura.');
    }
}
