<?php

namespace App\Support;

use App\Models\Proyecto;
use Carbon\Carbon;

class ProyectoDashboardService
{
    public function build(Proyecto $proyecto): array
    {
        $today = Carbon::now('America/Lima')->startOfDay();
        $monthStart = $today->copy()->startOfMonth();
        $monthEnd = $today->copy()->endOfMonth();

        return [
            'project' => $this->buildProjectSummary($proyecto),
            'lotes' => $this->buildLoteSummary($proyecto),
            'clientes' => $this->buildClienteSummary($proyecto),
            'financial' => $this->buildFinancialSummary($proyecto, $today, $monthStart, $monthEnd),
            'cobranza' => $this->buildCobranzaSummary($proyecto, $today, $monthStart, $monthEnd),
            'documents' => $this->buildDocumentSummary($proyecto),
            'recents' => $this->buildRecentActivity($proyecto),
            'charts' => $this->buildCharts($proyecto, $today),
        ];
    }

    protected function buildProjectSummary(Proyecto $proyecto): array
    {
        return [
            'nombre' => $proyecto->nombre,
            'estado' => $proyecto->estado,
            'ubicacion' => $proyecto->direccion ?: $proyecto->ubicacion ?: 'Ubicacion por definir',
            'descripcion' => $proyecto->descripcion,
            'fecha_inicio' => $proyecto->fecha_inicio,
            'fecha_lanzamiento' => $proyecto->fecha_lanzamiento,
        ];
    }

    protected function buildLoteSummary(Proyecto $proyecto): array
    {
        $counts = $proyecto->lotes()
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return [
            'total_lotes' => (int) $proyecto->lotes()->count(),
            'lotes_libres' => (int) ($counts['Libre'] ?? 0),
            'lotes_reservados' => (int) ($counts['Reservado'] ?? 0),
            'lotes_financiamiento' => (int) ($counts['Financiamiento'] ?? 0),
            'lotes_vendidos' => (int) ($counts['Vendido'] ?? 0),
        ];
    }

    protected function buildClienteSummary(Proyecto $proyecto): array
    {
        $estadoCounts = $proyecto->clientes()
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $cobranzaCounts = $proyecto->clientes()
            ->where('estado', 'activo')
            ->selectRaw('estado_cobranza, COUNT(*) as total')
            ->groupBy('estado_cobranza')
            ->pluck('total', 'estado_cobranza');

        return [
            'total_clientes' => (int) $proyecto->clientes()->count(),
            'clientes_activos' => (int) ($estadoCounts['activo'] ?? 0),
            'clientes_reserva' => (int) ($cobranzaCounts['reservado'] ?? 0),
            'clientes_financiamiento' => (int) ($cobranzaCounts['financiamiento'] ?? 0),
            'clientes_pagados' => (int) ($cobranzaCounts['pagado'] ?? 0),
            'clientes_desistidos' => (int) (($estadoCounts['desistido'] ?? 0) + ($estadoCounts['anulado'] ?? 0)),
        ];
    }

    protected function buildFinancialSummary(Proyecto $proyecto, Carbon $today, Carbon $monthStart, Carbon $monthEnd): array
    {
        $pagosValidos = $this->pagosValidosQuery($proyecto);
        $ingresosRegistrados = $this->ingresosRegistradosQuery($proyecto);
        $egresosRegistrados = $this->egresosRegistradosQuery($proyecto);

        $ingresosMes = round((float) (clone $ingresosRegistrados)
            ->whereBetween('fecha_ingreso', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->sum('monto'), 2);

        $egresosMes = round((float) (clone $egresosRegistrados)
            ->whereBetween('fecha', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->sum('monto'), 2);

        return [
            'total_cobrado_historico' => round((float) (clone $pagosValidos)->sum('monto'), 2),
            'saldo_pendiente_total' => round((float) $proyecto->clientes()->where('estado', 'activo')->sum('saldo_pendiente'), 2),
            'ingresos_mes' => $ingresosMes,
            'egresos_mes' => $egresosMes,
            'flujo_neto_mes' => round($ingresosMes - $egresosMes, 2),
            'ingresos_hoy' => round((float) (clone $ingresosRegistrados)->whereDate('fecha_ingreso', $today->toDateString())->sum('monto'), 2),
            'egresos_hoy' => round((float) (clone $egresosRegistrados)->whereDate('fecha', $today->toDateString())->sum('monto'), 2),
        ];
    }

    protected function buildCobranzaSummary(Proyecto $proyecto, Carbon $today, Carbon $monthStart, Carbon $monthEnd): array
    {
        $pagosValidos = $this->pagosValidosQuery($proyecto);
        $cronograma = $proyecto->cronogramaPagos();

        $ultimoPago = (clone $pagosValidos)
            ->with(['cliente', 'lote'])
            ->latest('fecha_pago')
            ->latest('id')
            ->first();

        return [
            'cuotas_pendientes' => (int) (clone $cronograma)
                ->where('estado', 'pendiente')
                ->whereDate('fecha_vencimiento', '>=', $today->toDateString())
                ->count(),
            'cuotas_vencidas' => (int) (clone $cronograma)
                ->where(function ($query) use ($today) {
                    $query->where('estado', 'vencido')
                        ->orWhere(function ($inner) use ($today) {
                            $inner->where('estado', 'pendiente')
                                ->whereDate('fecha_vencimiento', '<', $today->toDateString());
                        });
                })
                ->count(),
            'clientes_con_saldo' => (int) $proyecto->clientes()
                ->where('estado', 'activo')
                ->where('saldo_pendiente', '>', 0)
                ->count(),
            'ultimo_pago' => $ultimoPago,
            'total_pagado_mes' => round((float) (clone $pagosValidos)
                ->whereBetween('fecha_pago', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->sum('monto'), 2),
            'cantidad_pagos_mes' => (int) (clone $pagosValidos)
                ->whereBetween('fecha_pago', [$monthStart->toDateString(), $monthEnd->toDateString()])
                ->count(),
        ];
    }

    protected function buildDocumentSummary(Proyecto $proyecto): array
    {
        $tipos = $proyecto->documentos()
            ->where('estado', 'activo')
            ->selectRaw('tipo_documento, COUNT(*) as total')
            ->groupBy('tipo_documento')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'tipo' => $row->tipo_documento,
                'total' => (int) $row->total,
            ])
            ->all();

        return [
            'documentos_total' => (int) $proyecto->documentos()->where('estado', 'activo')->count(),
            'por_tipo' => $tipos,
        ];
    }

    protected function buildRecentActivity(Proyecto $proyecto): array
    {
        return [
            'pagos' => $this->pagosValidosQuery($proyecto)
                ->with(['cliente', 'lote'])
                ->latest('fecha_pago')
                ->latest('id')
                ->take(5)
                ->get(),
            'ingresos' => $this->ingresosRegistradosQuery($proyecto)
                ->with(['cliente', 'lote'])
                ->latest('fecha_ingreso')
                ->latest('id')
                ->take(5)
                ->get(),
            'egresos' => $this->egresosRegistradosQuery($proyecto)
                ->latest('fecha')
                ->latest('id')
                ->take(5)
                ->get(),
            'documentos' => $proyecto->documentos()
                ->where('estado', 'activo')
                ->with(['cliente', 'lote'])
                ->latest('created_at')
                ->take(5)
                ->get(),
            'clientes' => $proyecto->clientes()
                ->with('lote')
                ->latest('created_at')
                ->take(5)
                ->get(),
        ];
    }

    protected function buildCharts(Proyecto $proyecto, Carbon $today): array
    {
        $start = $today->copy()->startOfMonth()->subMonths(5);
        $end = $today->copy()->startOfMonth();

        return [
            'lotes_estado' => $this->buildLotesChart($proyecto),
            'ingresos_egresos_mensual' => $this->buildMonthlyCashChart($proyecto, $start, $end),
            'cobranza_periodo' => $this->buildCobranzaChart($proyecto, $start, $end),
            'cuotas_estado' => $this->buildCuotasChart($proyecto, $today),
        ];
    }

    protected function buildLotesChart(Proyecto $proyecto): array
    {
        $summary = $this->buildLoteSummary($proyecto);

        return [
            'labels' => ['Libres', 'Reservados', 'Financiamiento', 'Vendidos'],
            'data' => [
                $summary['lotes_libres'],
                $summary['lotes_reservados'],
                $summary['lotes_financiamiento'],
                $summary['lotes_vendidos'],
            ],
        ];
    }

    protected function buildMonthlyCashChart(Proyecto $proyecto, Carbon $start, Carbon $end): array
    {
        $ingresos = $this->ingresosRegistradosQuery($proyecto)
            ->whereBetween('fecha_ingreso', [$start->copy()->startOfMonth()->toDateString(), $end->copy()->endOfMonth()->toDateString()])
            ->selectRaw("DATE_FORMAT(fecha_ingreso, '%Y-%m') as bucket, SUM(monto) as total")
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $egresos = $this->egresosRegistradosQuery($proyecto)
            ->whereBetween('fecha', [$start->copy()->startOfMonth()->toDateString(), $end->copy()->endOfMonth()->toDateString()])
            ->selectRaw("DATE_FORMAT(fecha, '%Y-%m') as bucket, SUM(monto) as total")
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $labels = [];
        $serieIngresos = [];
        $serieEgresos = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $bucket = $cursor->format('Y-m');
            $labels[] = $cursor->locale('es')->translatedFormat('M Y');
            $serieIngresos[] = round((float) ($ingresos[$bucket] ?? 0), 2);
            $serieEgresos[] = round((float) ($egresos[$bucket] ?? 0), 2);
            $cursor->addMonthNoOverflow();
        }

        return [
            'labels' => $labels,
            'ingresos' => $serieIngresos,
            'egresos' => $serieEgresos,
        ];
    }

    protected function buildCobranzaChart(Proyecto $proyecto, Carbon $start, Carbon $end): array
    {
        $cobranza = $this->pagosValidosQuery($proyecto)
            ->whereBetween('fecha_pago', [$start->copy()->startOfMonth()->toDateString(), $end->copy()->endOfMonth()->toDateString()])
            ->selectRaw("DATE_FORMAT(fecha_pago, '%Y-%m') as bucket, SUM(monto) as total")
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $labels = [];
        $serie = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $bucket = $cursor->format('Y-m');
            $labels[] = $cursor->locale('es')->translatedFormat('M Y');
            $serie[] = round((float) ($cobranza[$bucket] ?? 0), 2);
            $cursor->addMonthNoOverflow();
        }

        return [
            'labels' => $labels,
            'data' => $serie,
        ];
    }

    protected function buildCuotasChart(Proyecto $proyecto, Carbon $today): array
    {
        $base = $proyecto->cronogramaPagos();

        $pagadas = (int) (clone $base)->where('estado', 'pagado')->count();
        $vencidas = (int) (clone $base)
            ->where(function ($query) use ($today) {
                $query->where('estado', 'vencido')
                    ->orWhere(function ($inner) use ($today) {
                        $inner->where('estado', 'pendiente')
                            ->whereDate('fecha_vencimiento', '<', $today->toDateString());
                    });
            })
            ->count();
        $pendientes = (int) (clone $base)
            ->where('estado', 'pendiente')
            ->whereDate('fecha_vencimiento', '>=', $today->toDateString())
            ->count();

        return [
            'labels' => ['Pagadas', 'Pendientes', 'Vencidas'],
            'data' => [$pagadas, $pendientes, $vencidas],
        ];
    }

    protected function pagosValidosQuery(Proyecto $proyecto)
    {
        return $proyecto->pagos()
            ->where('estado_pago', 'registrado')
            ->whereIn('tipo_pago', ['reserva', 'inicial', 'cuota', 'contado']);
    }

    protected function ingresosRegistradosQuery(Proyecto $proyecto)
    {
        return $proyecto->ingresos()->where('estado', 'registrado');
    }

    protected function egresosRegistradosQuery(Proyecto $proyecto)
    {
        return $proyecto->egresos()->where('estado', 'registrado');
    }
}
