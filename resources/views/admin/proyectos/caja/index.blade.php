@extends('layouts.admin-project', ['currentModule' => 'caja'])

@section('title', 'Caja | ' . $proyecto->nombre)
@section('module_label', 'Caja / Flujo de Caja')
@section('page_title', 'Caja de ' . $proyecto->nombre)
@section('page_subtitle', 'Analiza ingresos y egresos del proyecto en una sola pantalla, con flujo neto, tendencias y movimientos consolidados listos para crecer hacia una futura caja general.')

@push('styles')
<style>
    .summary-grid-caja{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:22px;}
    .metric-card{padding:20px;border-radius:20px;background:#fff;border:1px solid var(--border);box-shadow:0 10px 30px rgba(15,23,42,.05);}
    .metric-label{font-size:11px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);}
    .metric-value{margin-top:10px;font-size:30px;font-weight:900;line-height:1;color:var(--text);}
    .metric-helper{margin-top:6px;font-size:12px;color:var(--gray);}
    .metric-card.is-income .metric-value{color:#15803d;}
    .metric-card.is-expense .metric-value{color:#b91c1c;}
    .metric-card.is-flow-positive .metric-value{color:#1d4ed8;}
    .metric-card.is-flow-negative .metric-value{color:#be123c;}
    .metric-card.is-neutral .metric-value{color:var(--vt);}
    .toolbar-form{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin-bottom:18px;}
    .toolbar-select{width:100%;border:1.5px solid var(--border);background:#fff;border-radius:14px;padding:12px 14px;font:600 13px 'Poppins',sans-serif;color:var(--text);}
    .toolbar-actions{display:flex;gap:10px;flex-wrap:wrap;grid-column:1 / -1;}
    .helper-banner{padding:14px 16px;border-radius:16px;background:#f8f7ff;border:1px solid rgba(85,51,204,.12);font-size:12px;color:var(--gray);margin-bottom:18px;line-height:1.6;}
    .helper-banner strong{color:var(--text);}
    .charts-grid{display:grid;grid-template-columns:1.4fr .9fr;gap:18px;margin-bottom:22px;}
    .chart-card{padding:20px;}
    .chart-title{font-size:16px;font-weight:800;color:var(--text);}
    .chart-subtitle{font-size:12px;color:var(--gray);margin-top:4px;margin-bottom:16px;}
    .chart-box{position:relative;min-height:320px;}
    .chart-box canvas{width:100% !important;height:320px !important;}
    .chart-box.is-small canvas{height:280px !important;}
    .chart-empty{display:flex;align-items:center;justify-content:center;height:100%;min-height:260px;text-align:center;color:var(--gray);font-size:13px;}
    .movements-card{padding:22px;}
    .movement-badge,.origin-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;}
    .movement-badge::before,.origin-badge::before{content:'';width:8px;height:8px;border-radius:50%;}
    .movement-badge.ingreso{background:#dcfce7;color:#15803d;}
    .movement-badge.ingreso::before{background:#16a34a;}
    .movement-badge.egreso{background:#fee2e2;color:#b91c1c;}
    .movement-badge.egreso::before{background:#dc2626;}
    .origin-badge.cobranza{background:#dbeafe;color:#1d4ed8;}
    .origin-badge.cobranza::before{background:#2563eb;}
    .origin-badge.manual,.origin-badge.caja_personal{background:#f3e8ff;color:#6d28d9;}
    .origin-badge.manual::before,.origin-badge.caja_personal::before{background:#7c3aed;}
    .origin-badge.caja_chica{background:#fef3c7;color:#b45309;}
    .origin-badge.caja_chica::before{background:#d97706;}
    .origin-badge.caja_general{background:#ecfeff;color:#0f766e;}
    .origin-badge.caja_general::before{background:#0d9488;}
    @media(max-width:1180px){.summary-grid-caja{grid-template-columns:repeat(3,minmax(0,1fr));}.toolbar-form{grid-template-columns:repeat(3,minmax(0,1fr));}.charts-grid{grid-template-columns:1fr;}}
    @media(max-width:860px){.summary-grid-caja{grid-template-columns:repeat(2,minmax(0,1fr));}.toolbar-form{grid-template-columns:repeat(2,minmax(0,1fr));}}
    @media(max-width:640px){.summary-grid-caja,.toolbar-form{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')
@php
    $flowClass = $metrics['flujo_neto'] > 0 ? 'is-flow-positive' : ($metrics['flujo_neto'] < 0 ? 'is-flow-negative' : 'is-neutral');
@endphp

<section class="summary-grid-caja">
    <article class="metric-card is-income">
        <div class="metric-label">Total Ingresos</div>
        <div class="metric-value">S/. {{ number_format((float) $metrics['total_ingresos'], 2, '.', ',') }}</div>
        <div class="metric-helper">Periodo filtrado</div>
    </article>
    <article class="metric-card is-expense">
        <div class="metric-label">Total Egresos</div>
        <div class="metric-value">S/. {{ number_format((float) $metrics['total_egresos'], 2, '.', ',') }}</div>
        <div class="metric-helper">Periodo filtrado</div>
    </article>
    <article class="metric-card {{ $flowClass }}">
        <div class="metric-label">Flujo Neto</div>
        <div class="metric-value">S/. {{ number_format((float) $metrics['flujo_neto'], 2, '.', ',') }}</div>
        <div class="metric-helper">Ingresos menos egresos</div>
    </article>
    <article class="metric-card is-neutral">
        <div class="metric-label">Movimientos</div>
        <div class="metric-value">{{ number_format((int) $metrics['total_movimientos']) }}</div>
        <div class="metric-helper">Ticket alto: S/. {{ number_format((float) $metrics['ticket_alto_ingreso'], 2, '.', ',') }}</div>
    </article>
    <article class="metric-card is-income">
        <div class="metric-label">Ingresos Hoy</div>
        <div class="metric-value">S/. {{ number_format((float) $metrics['ingresos_hoy'], 2, '.', ',') }}</div>
        <div class="metric-helper">Corte diario</div>
    </article>
    <article class="metric-card is-expense">
        <div class="metric-label">Egresos Hoy</div>
        <div class="metric-value">S/. {{ number_format((float) $metrics['egresos_hoy'], 2, '.', ',') }}</div>
        <div class="metric-helper">Corte diario</div>
    </article>
    <article class="metric-card is-income">
        <div class="metric-label">Ingresos Mes</div>
        <div class="metric-value">S/. {{ number_format((float) $metrics['ingresos_mes'], 2, '.', ',') }}</div>
        <div class="metric-helper">Mes actual</div>
    </article>
    <article class="metric-card is-expense">
        <div class="metric-label">Egresos Mes</div>
        <div class="metric-value">S/. {{ number_format((float) $metrics['egresos_mes'], 2, '.', ',') }}</div>
        <div class="metric-helper">Mes actual</div>
    </article>
</section>

<section class="card content-card">
    <div class="section-head">
        <div class="section-title">Filtros de <span>Caja</span></div>
    </div>

    <div class="helper-banner">
        <strong>Regla actual del flujo:</strong> los filtros de fecha impactan metricas, graficos y tabla.
        El filtro por <strong>cliente</strong> concentra la vista en ingresos asociados a ese cliente.
        El filtro por <strong>categoria de egreso</strong> concentra la vista en esa salida de dinero.
    </div>

    <form method="GET" action="{{ route('admin.proyectos.caja', $proyecto) }}" class="toolbar-form">
        <input type="date" name="fecha_inicio" value="{{ optional($filters['fecha_inicio'])->format('Y-m-d') }}" class="toolbar-select">
        <input type="date" name="fecha_fin" value="{{ optional($filters['fecha_fin'])->format('Y-m-d') }}" class="toolbar-select">

        <select name="cliente_id" class="toolbar-select">
            <option value="">Todos los clientes</option>
            @foreach($clientes as $cliente)
            <option value="{{ $cliente->id }}" @selected((int) ($filters['cliente_id'] ?? 0) === (int) $cliente->id)>{{ $cliente->nombre_completo }}</option>
            @endforeach
        </select>

        <select name="categoria_egreso" class="toolbar-select">
            <option value="">Todas las categorias de egreso</option>
            @foreach($categoriasEgreso as $categoria)
            <option value="{{ $categoria }}" @selected(($filters['categoria_egreso'] ?? null) === $categoria)>{{ $categoria }}</option>
            @endforeach
        </select>

        <select name="tipo_movimiento" class="toolbar-select">
            <option value="">Ingresos y egresos</option>
            @foreach($tiposMovimiento as $key => $label)
            <option value="{{ $key }}" @selected(($filters['tipo_movimiento'] ?? null) === $key)>{{ $label }}</option>
            @endforeach
        </select>

        <div class="toolbar-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> Aplicar filtros</button>
            <a href="{{ route('admin.proyectos.caja', $proyecto) }}" class="btn-secondary">Limpiar filtros</a>
        </div>
    </form>
</section>

<section class="charts-grid">
    <article class="card chart-card">
        <div class="chart-title">Ingresos vs egresos por dia</div>
        <div class="chart-subtitle">Rango diario: {{ $charts['diario']['desde'] }} al {{ $charts['diario']['hasta'] }}</div>
        <div class="chart-box">
            <canvas id="dailyCashChart"></canvas>
        </div>
    </article>

    <article class="card chart-card">
        <div class="chart-title">Distribucion de egresos por categoria</div>
        <div class="chart-subtitle">Solo considera egresos registrados del filtro actual.</div>
        <div class="chart-box is-small">
            @if(empty($charts['egresos_categoria']['labels']))
            <div class="chart-empty">No hay egresos para graficar con los filtros actuales.</div>
            @else
            <canvas id="expenseDistributionChart"></canvas>
            @endif
        </div>
    </article>
</section>

<section class="card chart-card" style="margin-bottom:22px;">
    <div class="chart-title">Ingresos vs egresos por mes</div>
    <div class="chart-subtitle">Vista mensual para seguir tendencia y estacionalidad del proyecto.</div>
    <div class="chart-box">
        <canvas id="monthlyCashChart"></canvas>
    </div>
</section>

<section class="card movements-card">
    <div class="section-head">
        <div class="section-title">Movimientos <span>Consolidados</span></div>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Concepto</th>
                    <th>Cliente / Responsable</th>
                    <th>Categoria</th>
                    <th>Monto</th>
                    <th>Origen</th>
                    <th>Referencia</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($movement->fecha)->format('d/m/Y') }}</td>
                    <td>
                        <span class="movement-badge {{ strtolower($movement->tipo_movimiento) }}">
                            {{ $movement->tipo_movimiento }}
                        </span>
                    </td>
                    <td class="cell-strong">{{ $movement->concepto }}</td>
                    <td>{{ $movement->tercero }}</td>
                    <td>{{ \Illuminate\Support\Str::headline((string) $movement->categoria) }}</td>
                    <td class="cell-strong">S/. {{ number_format((float) $movement->monto, 2, '.', ',') }}</td>
                    <td>
                        <span class="origin-badge {{ $movement->origen }}">
                            {{ \Illuminate\Support\Str::headline((string) $movement->origen) }}
                        </span>
                    </td>
                    <td>{{ $movement->referencia }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <i class="fas fa-chart-line"></i>
                            <strong>No hay movimientos para mostrar con los filtros actuales.</strong>
                            <div style="margin-top:6px;">Registra ingresos o egresos en este proyecto para comenzar a construir el flujo de caja.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($movements->hasPages())
    <div class="pagination">
        <div class="pagination-status">
            Mostrando {{ $movements->firstItem() }} a {{ $movements->lastItem() }} de {{ $movements->total() }} movimientos
        </div>
        <div class="pagination-links">
            <a href="{{ $movements->previousPageUrl() ?: '#' }}" class="page-link {{ $movements->onFirstPage() ? 'disabled' : '' }}">
                <i class="fas fa-arrow-left"></i> Anterior
            </a>
            <a href="{{ $movements->hasMorePages() ? $movements->nextPageUrl() : '#' }}" class="page-link {{ $movements->hasMorePages() ? '' : 'disabled' }}">
                Siguiente <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    @endif
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof Chart === 'undefined') {
            return;
        }

        const commonCurrency = (value) => `S/. ${Number(value || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

        const dailyContext = document.getElementById('dailyCashChart');
        if (dailyContext) {
            new Chart(dailyContext, {
                type: 'line',
                data: {
                    labels: @json($charts['diario']['labels']),
                    datasets: [
                        {
                            label: 'Ingresos',
                            data: @json($charts['diario']['ingresos']),
                            borderColor: '#16a34a',
                            backgroundColor: 'rgba(22, 163, 74, 0.15)',
                            tension: 0.35,
                            fill: true,
                        },
                        {
                            label: 'Egresos',
                            data: @json($charts['diario']['egresos']),
                            borderColor: '#dc2626',
                            backgroundColor: 'rgba(220, 38, 38, 0.12)',
                            tension: 0.35,
                            fill: true,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.dataset.label}: ${commonCurrency(ctx.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: (value) => commonCurrency(value)
                            }
                        }
                    }
                }
            });
        }

        const monthlyContext = document.getElementById('monthlyCashChart');
        if (monthlyContext) {
            new Chart(monthlyContext, {
                type: 'bar',
                data: {
                    labels: @json($charts['mensual']['labels']),
                    datasets: [
                        {
                            label: 'Ingresos',
                            data: @json($charts['mensual']['ingresos']),
                            backgroundColor: 'rgba(22, 163, 74, 0.78)',
                            borderRadius: 8,
                        },
                        {
                            label: 'Egresos',
                            data: @json($charts['mensual']['egresos']),
                            backgroundColor: 'rgba(220, 38, 38, 0.72)',
                            borderRadius: 8,
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.dataset.label}: ${commonCurrency(ctx.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => commonCurrency(value)
                            }
                        }
                    }
                }
            });
        }

        const distributionContext = document.getElementById('expenseDistributionChart');
        if (distributionContext) {
            new Chart(distributionContext, {
                type: 'doughnut',
                data: {
                    labels: @json($charts['egresos_categoria']['labels']),
                    datasets: [{
                        data: @json($charts['egresos_categoria']['data']),
                        backgroundColor: ['#5533CC', '#EE00BB', '#2563eb', '#d97706', '#10b981', '#dc2626', '#0f766e', '#7c3aed', '#ea580c'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.label}: ${commonCurrency(ctx.parsed)}`
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
