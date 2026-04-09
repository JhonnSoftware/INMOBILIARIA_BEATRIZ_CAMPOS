<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Egreso;
use App\Models\Proyecto;
use App\Support\EgresoCatalog;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator as PaginatorInstance;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProyectoCajaController extends Controller
{
    public function index(Proyecto $proyecto): View
    {
        $filters = $this->normalizeFilters(request()->only([
            'fecha_inicio',
            'fecha_fin',
            'cliente_id',
            'categoria_egreso',
            'tipo_movimiento',
        ]));

        $ingresosQuery = $this->ingresosQuery($proyecto, $filters);
        $egresosQuery = $this->egresosQuery($proyecto, $filters);

        return view('admin.proyectos.caja.index', [
            'proyecto' => $proyecto,
            'filters' => $filters,
            'metrics' => $this->buildMetrics($ingresosQuery, $egresosQuery, $filters),
            'charts' => [
                'diario' => $this->buildDailyChart($proyecto, $filters),
                'mensual' => $this->buildMonthlyChart($proyecto, $filters),
                'egresos_categoria' => $this->buildExpenseDistribution($proyecto, $filters),
            ],
            'movements' => $this->buildMovements($proyecto, $filters),
            'clientes' => $proyecto->clientes()->orderBy('apellidos')->orderBy('nombres')->get(),
            'categoriasEgreso' => EgresoCatalog::subcategorias(),
            'tiposMovimiento' => [
                'ingreso' => 'Solo ingresos',
                'egreso' => 'Solo egresos',
            ],
        ]);
    }

    protected function buildMetrics($ingresosQuery, $egresosQuery, array $filters): array
    {
        $now = Carbon::now('America/Lima');
        $today = $now->toDateString();
        $monthStart = $now->copy()->startOfMonth()->toDateString();
        $monthEnd = $now->copy()->endOfMonth()->toDateString();

        $totalIngresos = round((float) (clone $ingresosQuery)->sum('monto'), 2);
        $totalEgresos = round((float) (clone $egresosQuery)->sum('monto'), 2);

        return [
            'total_ingresos' => $totalIngresos,
            'total_egresos' => $totalEgresos,
            'flujo_neto' => round($totalIngresos - $totalEgresos, 2),
            'ingresos_hoy' => round((float) (clone $ingresosQuery)->whereDate('fecha_ingreso', $today)->sum('monto'), 2),
            'egresos_hoy' => round((float) (clone $egresosQuery)->whereDate('fecha', $today)->sum('monto'), 2),
            'ingresos_mes' => round((float) (clone $ingresosQuery)->whereBetween('fecha_ingreso', [$monthStart, $monthEnd])->sum('monto'), 2),
            'egresos_mes' => round((float) (clone $egresosQuery)->whereBetween('fecha', [$monthStart, $monthEnd])->sum('monto'), 2),
            'ticket_alto_ingreso' => round((float) ((clone $ingresosQuery)->max('monto') ?? 0), 2),
            'total_movimientos' => (int) (clone $ingresosQuery)->count() + (int) (clone $egresosQuery)->count(),
            'fecha_inicio' => $filters['fecha_inicio']?->toDateString(),
            'fecha_fin' => $filters['fecha_fin']?->toDateString(),
        ];
    }

    protected function buildDailyChart(Proyecto $proyecto, array $filters): array
    {
        [$start, $end] = $this->resolveDailyRange($filters);

        $ingresos = $this->ingresosQuery($proyecto, [
            ...$filters,
            'fecha_inicio' => $start,
            'fecha_fin' => $end,
        ])
            ->selectRaw('DATE(fecha_ingreso) as bucket, SUM(monto) as total')
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $egresos = $this->egresosQuery($proyecto, [
            ...$filters,
            'fecha_inicio' => $start,
            'fecha_fin' => $end,
        ])
            ->selectRaw('DATE(fecha) as bucket, SUM(monto) as total')
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $labels = [];
        $incomeSeries = [];
        $expenseSeries = [];

        foreach (CarbonPeriod::create($start, $end) as $day) {
            $bucket = $day->format('Y-m-d');
            $labels[] = $day->format('d M');
            $incomeSeries[] = round((float) ($ingresos[$bucket] ?? 0), 2);
            $expenseSeries[] = round((float) ($egresos[$bucket] ?? 0), 2);
        }

        return [
            'labels' => $labels,
            'ingresos' => $incomeSeries,
            'egresos' => $expenseSeries,
            'desde' => $start->toDateString(),
            'hasta' => $end->toDateString(),
        ];
    }

    protected function buildMonthlyChart(Proyecto $proyecto, array $filters): array
    {
        [$start, $end] = $this->resolveMonthlyRange($filters);

        $ingresos = $this->ingresosQuery($proyecto, [
            ...$filters,
            'fecha_inicio' => $start->copy()->startOfMonth(),
            'fecha_fin' => $end->copy()->endOfMonth(),
        ])
            ->selectRaw("DATE_FORMAT(fecha_ingreso, '%Y-%m') as bucket, SUM(monto) as total")
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $egresos = $this->egresosQuery($proyecto, [
            ...$filters,
            'fecha_inicio' => $start->copy()->startOfMonth(),
            'fecha_fin' => $end->copy()->endOfMonth(),
        ])
            ->selectRaw("DATE_FORMAT(fecha, '%Y-%m') as bucket, SUM(monto) as total")
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $labels = [];
        $incomeSeries = [];
        $expenseSeries = [];
        $cursor = $start->copy()->startOfMonth();

        while ($cursor->lte($end)) {
            $bucket = $cursor->format('Y-m');
            $labels[] = $cursor->translatedFormat('M Y');
            $incomeSeries[] = round((float) ($ingresos[$bucket] ?? 0), 2);
            $expenseSeries[] = round((float) ($egresos[$bucket] ?? 0), 2);
            $cursor->addMonthNoOverflow();
        }

        return [
            'labels' => $labels,
            'ingresos' => $incomeSeries,
            'egresos' => $expenseSeries,
        ];
    }

    protected function buildExpenseDistribution(Proyecto $proyecto, array $filters): array
    {
        $distribution = $this->egresosQuery($proyecto, $filters)
            ->select('categoria')
            ->selectRaw('SUM(monto) as total')
            ->groupBy('categoria')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $distribution->pluck('categoria')->values()->all(),
            'data' => $distribution->pluck('total')->map(fn ($value) => round((float) $value, 2))->values()->all(),
        ];
    }

    protected function buildMovements(Proyecto $proyecto, array $filters): LengthAwarePaginator
    {
        $ingresos = $this->ingresosQuery($proyecto, $filters)
            ->leftJoin('clientes', 'clientes.id', '=', 'ingresos.cliente_id')
            ->leftJoin('lotes', 'lotes.id', '=', 'ingresos.lote_id')
            ->selectRaw("
                ingresos.id as orden_id,
                ingresos.fecha_ingreso as fecha,
                'Ingreso' as tipo_movimiento,
                ingresos.concepto as concepto,
                CASE
                    WHEN TRIM(CONCAT(COALESCE(clientes.nombres, ''), ' ', COALESCE(clientes.apellidos, ''))) = ''
                    THEN '-'
                    ELSE TRIM(CONCAT(COALESCE(clientes.nombres, ''), ' ', COALESCE(clientes.apellidos, '')))
                END as tercero,
                ingresos.tipo_ingreso as categoria,
                ingresos.monto as monto,
                ingresos.origen as origen,
                CASE
                    WHEN ingresos.pago_id IS NOT NULL THEN CONCAT('Pago #', ingresos.pago_id)
                    WHEN lotes.id IS NOT NULL THEN CONCAT('Mz. ', COALESCE(lotes.manzana, ''), ' - Lt. ', COALESCE(lotes.numero, ''))
                    ELSE '-'
                END as referencia
            ");

        $egresos = $this->egresosQuery($proyecto, $filters)
            ->selectRaw("
                egresos.id as orden_id,
                egresos.fecha as fecha,
                'Egreso' as tipo_movimiento,
                COALESCE(NULLIF(egresos.descripcion, ''), egresos.categoria) as concepto,
                COALESCE(NULLIF(egresos.responsable, ''), NULLIF(egresos.razon_social, ''), '-') as tercero,
                CONCAT(egresos.categoria_principal, ' / ', egresos.categoria) as categoria,
                egresos.monto as monto,
                egresos.fuente_dinero as origen,
                CASE
                    WHEN egresos.numero_comprobante IS NOT NULL AND egresos.numero_comprobante != ''
                    THEN CONCAT(COALESCE(egresos.tipo_comprobante, 'Comprobante'), ' ', IFNULL(CONCAT(egresos.serie_comprobante, '-'), ''), egresos.numero_comprobante)
                    WHEN egresos.ruc_proveedor IS NOT NULL AND egresos.ruc_proveedor != ''
                    THEN CONCAT('RUC ', egresos.ruc_proveedor)
                    ELSE '-'
                END as referencia
            ");

        $includeIngresos = ! $this->isIngresoSuppressed($filters);
        $includeEgresos = ! $this->isEgresoSuppressed($filters);

        if (! $includeIngresos && ! $includeEgresos) {
            return $this->emptyPaginator();
        }

        $baseQuery = match (true) {
            $includeIngresos && $includeEgresos => $ingresos->unionAll($egresos),
            $includeIngresos => $ingresos,
            default => $egresos,
        };

        return DB::query()
            ->fromSub($baseQuery, 'movimientos')
            ->orderByDesc('fecha')
            ->orderByDesc('orden_id')
            ->paginate(15)
            ->withQueryString();
    }

    protected function ingresosQuery(Proyecto $proyecto, array $filters): HasMany
    {
        return $proyecto->ingresos()
            ->where('ingresos.estado', 'registrado')
            ->when($this->isIngresoSuppressed($filters), fn ($query) => $query->whereRaw('1 = 0'))
            ->when($filters['fecha_inicio'], fn ($query, $date) => $query->whereDate('ingresos.fecha_ingreso', '>=', $date->toDateString()))
            ->when($filters['fecha_fin'], fn ($query, $date) => $query->whereDate('ingresos.fecha_ingreso', '<=', $date->toDateString()))
            ->when($filters['cliente_id'], fn ($query, $clienteId) => $query->where('ingresos.cliente_id', $clienteId));
    }

    protected function egresosQuery(Proyecto $proyecto, array $filters): HasMany
    {
        return $proyecto->egresos()
            ->where('egresos.estado', 'registrado')
            ->when($this->isEgresoSuppressed($filters), fn ($query) => $query->whereRaw('1 = 0'))
            ->when($filters['fecha_inicio'], fn ($query, $date) => $query->whereDate('egresos.fecha', '>=', $date->toDateString()))
            ->when($filters['fecha_fin'], fn ($query, $date) => $query->whereDate('egresos.fecha', '<=', $date->toDateString()))
            ->when($filters['categoria_egreso'], fn ($query, $categoria) => $query->where('egresos.categoria', $categoria));
    }

    protected function normalizeFilters(array $filters): array
    {
        $fechaInicio = $this->parseDate($filters['fecha_inicio'] ?? null);
        $fechaFin = $this->parseDate($filters['fecha_fin'] ?? null);

        if ($fechaInicio && $fechaFin && $fechaInicio->gt($fechaFin)) {
            [$fechaInicio, $fechaFin] = [$fechaFin, $fechaInicio];
        }

        $clienteId = ! empty($filters['cliente_id']) ? (int) $filters['cliente_id'] : null;
        $categoria = trim((string) ($filters['categoria_egreso'] ?? ''));
        $tipoMovimiento = trim((string) ($filters['tipo_movimiento'] ?? ''));
        $tipoMovimiento = in_array($tipoMovimiento, ['ingreso', 'egreso'], true) ? $tipoMovimiento : null;

        return [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'cliente_id' => $clienteId ?: null,
            'categoria_egreso' => $categoria !== '' ? $categoria : null,
            'tipo_movimiento' => $tipoMovimiento,
        ];
    }

    protected function parseDate(mixed $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value, 'America/Lima')->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    protected function resolveDailyRange(array $filters): array
    {
        $now = Carbon::now('America/Lima')->startOfDay();

        if ($filters['fecha_inicio'] && $filters['fecha_fin']) {
            return [$filters['fecha_inicio']->copy(), $filters['fecha_fin']->copy()];
        }

        if ($filters['fecha_inicio']) {
            $end = $filters['fecha_inicio']->lte($now) ? $now : $filters['fecha_inicio']->copy();
            return [$filters['fecha_inicio']->copy(), $end];
        }

        if ($filters['fecha_fin']) {
            return [$filters['fecha_fin']->copy()->subDays(29), $filters['fecha_fin']->copy()];
        }

        return [$now->copy()->subDays(29), $now];
    }

    protected function resolveMonthlyRange(array $filters): array
    {
        $now = Carbon::now('America/Lima')->startOfMonth();

        if ($filters['fecha_inicio'] && $filters['fecha_fin']) {
            return [$filters['fecha_inicio']->copy()->startOfMonth(), $filters['fecha_fin']->copy()->startOfMonth()];
        }

        if ($filters['fecha_inicio']) {
            $end = $filters['fecha_inicio']->lte($now) ? $now : $filters['fecha_inicio']->copy()->startOfMonth();
            return [$filters['fecha_inicio']->copy()->startOfMonth(), $end];
        }

        if ($filters['fecha_fin']) {
            return [$filters['fecha_fin']->copy()->startOfMonth()->subMonths(5), $filters['fecha_fin']->copy()->startOfMonth()];
        }

        return [$now->copy()->subMonths(5), $now];
    }

    protected function isIngresoSuppressed(array $filters): bool
    {
        return $filters['tipo_movimiento'] === 'egreso' || filled($filters['categoria_egreso']);
    }

    protected function isEgresoSuppressed(array $filters): bool
    {
        return $filters['tipo_movimiento'] === 'ingreso' || filled($filters['cliente_id']);
    }

    protected function emptyPaginator(): LengthAwarePaginator
    {
        return new PaginatorInstance(
            Collection::make(),
            0,
            15,
            Paginator::resolveCurrentPage(),
            [
                'path' => Paginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]
        );
    }
}
