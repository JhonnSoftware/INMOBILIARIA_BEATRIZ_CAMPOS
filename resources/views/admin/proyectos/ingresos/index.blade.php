@extends('layouts.admin-project', ['currentModule' => 'ingresos'])

@section('title', 'Ingresos | ' . $proyecto->nombre)
@section('module_label', 'Ingresos')
@section('page_title', 'Ingresos de ' . $proyecto->nombre)
@section('page_subtitle', 'Consolida los ingresos del proyecto, diferenciando cobranza y registros manuales.')

@push('styles')
<style>
/* ══ Stat cards ══ */
.stat-row { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 16px; margin-bottom: 22px; }
.stat-card {
    background: #fff; border-radius: 16px; padding: 20px 22px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    border-left: 5px solid var(--accent-color, #6366f1);
    display: flex; flex-direction: column; gap: 4px;
}
.stat-card .stat-label  { font-size: 12px; font-weight: 600; color: #6b7280; }
.stat-card .stat-amount { font-size: 26px; font-weight: 900; color: #111827; line-height: 1.1; margin: 4px 0; }
.stat-card .stat-sub    { font-size: 11px; color: #9ca3af; font-weight: 500; }
.stat-card.green  { --accent-color: #22c55e; }
.stat-card.red    { --accent-color: #ef4444; }
.stat-card.purple { --accent-color: #8b5cf6; }
.stat-card.orange { --accent-color: #f97316; }

/* ══ Action buttons row ══ */
.action-row { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; margin-bottom: 22px; }
.action-btn {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 13px 20px; border-radius: 12px; font: 700 13px 'Poppins', sans-serif;
    border: none; cursor: pointer; text-decoration: none; transition: opacity .2s, transform .15s;
}
.action-btn:hover { opacity: .88; transform: translateY(-1px); }
.action-btn.blue   { background: #2563eb; color: #fff; }
.action-btn.cyan   { background: #06b6d4; color: #fff; }
.action-btn.green  { background: #16a34a; color: #fff; }

/* ══ Tabs ══ */
.tab-nav { display: flex; gap: 0; border-bottom: 2px solid #e5e7eb; margin-bottom: 22px; }
.tab-btn {
    padding: 10px 20px; font: 600 13px 'Poppins', sans-serif; color: #6b7280;
    background: none; border: none; cursor: pointer; border-bottom: 3px solid transparent;
    margin-bottom: -2px; transition: color .2s, border-color .2s;
}
.tab-btn:hover { color: #2563eb; }
.tab-btn.active { color: #111827; font-weight: 800; border-bottom-color: #2563eb; }

/* ══ Tab panels ══ */
.tab-panel { display: none; }
.tab-panel.active { display: block; }

/* ══ Resumen panel ══ */
.resumen-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 18px; }
.chart-card { background: #fff; border-radius: 16px; padding: 22px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.chart-card h3 { font-size: 15px; font-weight: 800; color: #111827; margin-bottom: 4px; }
.chart-card .chart-sub { font-size: 12px; color: #9ca3af; margin-bottom: 16px; }
.stats-card { background: #fff; border-radius: 16px; padding: 22px; box-shadow: 0 2px 12px rgba(0,0,0,.06); display: flex; flex-direction: column; gap: 14px; }
.stats-card h3 { font-size: 15px; font-weight: 800; color: #111827; margin-bottom: 2px; }
.stats-item {
    border-left: 4px solid #2563eb; padding: 12px 16px;
    background: #f8fafc; border-radius: 0 12px 12px 0;
}
.stats-item.green-border { border-left-color: #22c55e; }
.stats-item h4 { font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 4px; }
.stats-item .big-num { font-size: 28px; font-weight: 900; color: #111827; line-height: 1; }
.stats-item p { font-size: 11px; color: #9ca3af; margin-top: 4px; }

/* ══ Últimos ingresos table ══ */
.ultimos-card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.06); margin-top: 18px; }
.ultimos-header {
    background: #06b6d4; padding: 14px 20px;
    display: flex; align-items: center; justify-content: space-between;
}
.ultimos-header span { font-size: 15px; font-weight: 800; color: #fff; }
.ultimos-header a { font-size: 12px; font-weight: 700; background: #fff; color: #0891b2; padding: 6px 14px; border-radius: 8px; text-decoration: none; }

/* ══ Listado / Filtros ══ */
.filter-card-inner { background: #fff; border-radius: 16px; padding: 22px; box-shadow: 0 2px 12px rgba(0,0,0,.06); margin-bottom: 18px; }
.filter-grid-inner { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; }
.filter-select {
    width: 100%; border: 1.5px solid #e5e7eb; background: #fff;
    border-radius: 12px; padding: 10px 14px; font: 600 13px 'Poppins', sans-serif; color: #374151;
}
.filter-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 14px; }
.btn-limpiar-f { display:inline-flex;align-items:center;gap:6px;padding:10px 18px;border-radius:12px;background:#ef4444;color:#fff;font:700 13px 'Poppins',sans-serif;text-decoration:none;border:none;cursor:pointer; }

/* ══ Badges ══ */
.badge-tipo,.badge-origen,.badge-est {
    display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:999px;font-size:11px;font-weight:700;
}
.badge-tipo::before,.badge-origen::before,.badge-est::before { content:'';width:7px;height:7px;border-radius:50%; }
.badge-tipo.cuota,.badge-tipo.cuota_inicial  { background:#dbeafe;color:#1d4ed8; } .badge-tipo.cuota::before,.badge-tipo.cuota_inicial::before { background:#2563eb; }
.badge-tipo.reserva  { background:#fef3c7;color:#b45309; } .badge-tipo.reserva::before { background:#d97706; }
.badge-tipo.contado  { background:#dcfce7;color:#15803d; } .badge-tipo.contado::before { background:#16a34a; }
.badge-tipo.extra,.badge-tipo.otro { background:#f3e8ff;color:#6d28d9; } .badge-tipo.extra::before,.badge-tipo.otro::before { background:#7c3aed; }
.badge-origen.cobranza { background:#dbeafe;color:#1d4ed8; } .badge-origen.cobranza::before { background:#2563eb; }
.badge-origen.manual   { background:#f3e8ff;color:#6d28d9; } .badge-origen.manual::before   { background:#7c3aed; }
.badge-est.registrado  { background:#dcfce7;color:#15803d; } .badge-est.registrado::before  { background:#16a34a; }
.badge-est.anulado     { background:#fee2e2;color:#b91c1c; } .badge-est.anulado::before     { background:#dc2626; }
.helper-row { display:flex;align-items:center;gap:8px;flex-wrap:wrap; }

/* ══ Modal filtrar ══ */
.modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(4px);z-index:1000;align-items:center;justify-content:center; }
.modal-overlay.open { display:flex; }
.modal-box-f { background:#fff;border-radius:20px;width:100%;max-width:600px;box-shadow:0 24px 60px rgba(0,0,0,.18);overflow:hidden;animation:modalIn .2s ease; }
@keyframes modalIn { from{transform:scale(.93) translateY(14px);opacity:0} to{transform:scale(1) translateY(0);opacity:1} }
.modal-box-f .mh { background:#2563eb;padding:18px 24px;display:flex;align-items:center;justify-content:space-between; }
.modal-box-f .mh span { font-size:15px;font-weight:800;color:#fff; }
.modal-box-f .mh button { background:rgba(255,255,255,.2);border:none;color:#fff;width:30px;height:30px;border-radius:8px;cursor:pointer;font-size:14px; }
.modal-box-f .mb { padding:22px 24px 24px; }
.modal-fgrid { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px; }
.modal-label { font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.6px;margin-bottom:5px; }
.modal-input,.modal-select { width:100%;border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 13px;font:600 13px 'Poppins',sans-serif;color:#374151; }

@media(max-width:1100px) { .stat-row,.resumen-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
@media(max-width:700px)  { .stat-row,.action-row,.filter-grid-inner,.modal-fgrid { grid-template-columns:1fr; } .resumen-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

{{-- ══ STAT CARDS ══ --}}
<div class="stat-row">
    <div class="stat-card green">
        <div class="stat-label">Ingresos Hoy</div>
        <div class="stat-amount">S/. {{ number_format((float)$resumen['hoy'], 2, '.', ',') }}</div>
        <div class="stat-sub">{{ now()->format('d/m/Y') }}</div>
    </div>
    <div class="stat-card red">
        <div class="stat-label">Ingresos Mes Actual</div>
        <div class="stat-amount">S/. {{ number_format((float)$resumen['mes'], 2, '.', ',') }}</div>
        <div class="stat-sub">{{ now()->translatedFormat('F Y') }}</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-label">Total Ingresos</div>
        <div class="stat-amount">S/. {{ number_format((float)$resumen['periodo'], 2, '.', ',') }}</div>
        <div class="stat-sub">Período seleccionado</div>
    </div>
    <div class="stat-card orange">
        <div class="stat-label">Ingreso Más Alto</div>
        <div class="stat-amount">S/. {{ number_format((float)$resumen['masAlto'], 2, '.', ',') }}</div>
        <div class="stat-sub">Valor máximo registrado</div>
    </div>
</div>

{{-- ══ ACTION BUTTONS ══ --}}
<div class="action-row" style="margin-bottom:22px;">
    <a href="{{ route('admin.proyectos.ingresos.create', $proyecto) }}" class="action-btn blue">
        <i class="fas fa-plus-circle"></i> Nuevo Ingreso
    </a>
    <button type="button" class="action-btn cyan" id="btnFiltrar">
        <i class="fas fa-filter"></i> Filtrar Datos
    </button>
    <button type="button" class="action-btn green" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimir Reporte
    </button>
</div>

{{-- ══ TABS ══ --}}
<div class="tab-nav">
    <button class="tab-btn active" data-tab="resumen">Resumen</button>
    <button class="tab-btn" data-tab="diarios">Ingresos Diarios</button>
    <button class="tab-btn" data-tab="mensuales">Ingresos Mensuales</button>
    <button class="tab-btn" data-tab="listado">Listado Completo</button>
</div>

{{-- ══════════ TAB: RESUMEN ══════════ --}}
<div class="tab-panel active" id="tab-resumen">
    <div class="resumen-grid">
        {{-- Gráfica diaria --}}
        <div class="chart-card">
            <h3>Resumen de Ingresos</h3>
            <div class="chart-sub">Ingresos por día del mes actual</div>
            <canvas id="chartDiario" height="220"></canvas>
            <div style="font-size:11px;color:#9ca3af;margin-top:12px;">
                * Datos del período: {{ now()->startOfMonth()->format('d/m/Y') }} - {{ now()->endOfMonth()->format('d/m/Y') }}
            </div>
        </div>

        {{-- Stats ══ --}}
        <div class="stats-card">
            <h3>Estadísticas Generales</h3>
            <div class="stats-item">
                <h4>Total de Ingresos</h4>
                <div class="big-num">S/. {{ number_format((float)$resumen['periodo'], 2, '.', ',') }}</div>
                <p>Período seleccionado</p>
            </div>
            <div class="stats-item green-border">
                <h4>Total de Registros</h4>
                <div class="big-num">{{ $resumen['cantidad'] }}</div>
                <p>Número de transacciones</p>
            </div>
            <div class="stats-item" style="border-left-color:#f97316;">
                <h4>Promedio por ingreso</h4>
                <div class="big-num" style="font-size:22px;">
                    S/. {{ $resumen['cantidad'] > 0 ? number_format($resumen['periodo'] / $resumen['cantidad'], 2, '.', ',') : '0.00' }}
                </div>
                <p>Monto promedio</p>
            </div>
            <div class="stats-item" style="border-left-color:#8b5cf6;">
                <h4>Origen cobranza / manual</h4>
                <div style="font-size:14px;font-weight:800;color:#111827;margin:4px 0;">
                    S/. {{ number_format((float)$resumen['cobranza'], 2, '.', ',') }}
                    <span style="font-size:11px;color:#9ca3af;font-weight:500;"> cobranza</span>
                </div>
                <div style="font-size:14px;font-weight:800;color:#111827;">
                    S/. {{ number_format((float)$resumen['manual'], 2, '.', ',') }}
                    <span style="font-size:11px;color:#9ca3af;font-weight:500;"> manual</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Últimos ingresos --}}
    <div class="ultimos-card">
        <div class="ultimos-header">
            <span>Últimos Ingresos Registrados</span>
            <a href="#" onclick="switchTab('listado');return false;">Ver todos</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Concepto</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ultimosIngresos as $ing)
                    <tr>
                        <td>{{ optional($ing->fecha_ingreso)->format('d/m/Y') }}</td>
                        <td>
                            @if($ing->cliente)
                            <div class="cell-strong">{{ $ing->cliente->nombre_completo }}</div>
                            @else
                            <span class="muted">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="cell-strong">{{ $ing->concepto }}</div>
                            @if($ing->descripcion)
                            <div class="muted">{{ \Illuminate\Support\Str::limit($ing->descripcion, 50) }}</div>
                            @endif
                        </td>
                        <td class="cell-strong">S/. {{ number_format((float)$ing->monto, 2, '.', ',') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:20px;color:#f97316;font-size:13px;">
                            No hay ingresos registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════ TAB: DIARIOS ══════════ --}}
<div class="tab-panel" id="tab-diarios">
    <div class="chart-card">
        <h3>Ingresos Diarios — {{ now()->translatedFormat('F Y') }}</h3>
        <div class="chart-sub">Monto registrado cada día del mes actual</div>
        <canvas id="chartDiario2" height="280"></canvas>
    </div>

    <div class="ultimos-card" style="margin-top:18px;">
        <div class="ultimos-header" style="background:#2563eb;">
            <span>Detalle por Día</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Fecha</th><th>Total</th><th>Registros</th></tr>
                </thead>
                <tbody>
                    @forelse($ingresosPorDia as $fecha => $total)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                        <td class="cell-strong">S/. {{ number_format((float)$total, 2, '.', ',') }}</td>
                        <td>—</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;padding:20px;color:#9ca3af;">Sin registros este mes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════ TAB: MENSUALES ══════════ --}}
<div class="tab-panel" id="tab-mensuales">
    <div class="chart-card">
        <h3>Ingresos Mensuales — {{ now()->year }}</h3>
        <div class="chart-sub">Evolución mensual del año {{ now()->year }}</div>
        <canvas id="chartMensual" height="280"></canvas>
    </div>

    <div class="ultimos-card" style="margin-top:18px;">
        <div class="ultimos-header" style="background:#8b5cf6;">
            <span>Detalle por Mes</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Mes</th><th>Total</th></tr>
                </thead>
                <tbody>
                    @php
                        $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                    @endphp
                    @forelse($ingresosPorMes as $mes => $total)
                    <tr>
                        <td>{{ $meses[(int)$mes] ?? $mes }}</td>
                        <td class="cell-strong">S/. {{ number_format((float)$total, 2, '.', ',') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" style="text-align:center;padding:20px;color:#9ca3af;">Sin registros este año.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════ TAB: LISTADO ══════════ --}}
<div class="tab-panel" id="tab-listado">
    <div class="filter-card-inner">
        <form method="GET" action="{{ route('admin.proyectos.ingresos', $proyecto) }}">
            <div class="filter-grid-inner">
                <select name="tipo_ingreso" class="filter-select">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos as $item)
                    <option value="{{ $item }}" @selected($tipo === $item)>{{ str_replace('_', ' ', ucfirst($item)) }}</option>
                    @endforeach
                </select>
                <select name="origen" class="filter-select">
                    <option value="">Todos los orígenes</option>
                    @foreach($origenes as $item)
                    <option value="{{ $item }}" @selected($origen === $item)>{{ ucfirst($item) }}</option>
                    @endforeach
                </select>
                <input type="date" name="desde" value="{{ $desde }}" class="filter-select">
                <input type="date" name="hasta" value="{{ $hasta }}" class="filter-select">
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
                <a href="{{ route('admin.proyectos.ingresos', $proyecto) }}" class="btn-limpiar-f">
                    <i class="fas fa-xmark"></i> Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="ultimos-card">
        <div class="ultimos-header" style="background:#374151;">
            <span>Listado Completo de Ingresos</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Concepto</th>
                        <th>Tipo</th>
                        <th>Origen</th>
                        <th>Cliente</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ingresos as $ingreso)
                    <tr>
                        <td>{{ optional($ingreso->fecha_ingreso)->format('d/m/Y') }}</td>
                        <td>
                            <div class="cell-strong">{{ $ingreso->concepto }}</div>
                            @if($ingreso->descripcion)
                            <div class="muted">{{ \Illuminate\Support\Str::limit($ingreso->descripcion, 60) }}</div>
                            @endif
                        </td>
                        <td><span class="badge-tipo {{ $ingreso->tipo_ingreso }}">{{ str_replace('_', ' ', ucfirst($ingreso->tipo_ingreso)) }}</span></td>
                        <td><span class="badge-origen {{ $ingreso->origen }}">{{ ucfirst($ingreso->origen) }}</span></td>
                        <td>
                            @if($ingreso->cliente)
                            <div class="cell-strong">{{ $ingreso->cliente->nombre_completo }}</div>
                            <div class="muted">{{ $ingreso->cliente->dni }}</div>
                            @else
                            <span class="muted">—</span>
                            @endif
                        </td>
                        <td class="cell-strong">S/. {{ number_format((float)$ingreso->monto, 2, '.', ',') }}</td>
                        <td><span class="badge-est {{ $ingreso->estado }}">{{ ucfirst($ingreso->estado) }}</span></td>
                        <td>
                            <div class="helper-row">
                                @if($ingreso->origen === 'manual')
                                <a href="{{ route('admin.proyectos.ingresos.edit', [$proyecto, $ingreso]) }}" class="btn-secondary">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.proyectos.ingresos.destroy', [$proyecto, $ingreso]) }}"
                                      onsubmit="return confirm('¿Eliminar este ingreso?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-secondary" style="color:#dc2626;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @elseif($ingreso->cliente_id)
                                <a href="{{ route('admin.proyectos.cobranza', ['proyecto' => $proyecto, 'cliente' => $ingreso->cliente_id]) }}" class="btn-secondary">
                                    <i class="fas fa-link"></i> Cobranza
                                </a>
                                @else
                                <span class="muted" style="font-size:12px;">Solo lectura</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-chart-line"></i>
                                <strong>No hay ingresos registrados.</strong>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ingresos->hasPages())
        <div class="pagination">
            <div class="pagination-status">
                Mostrando {{ $ingresos->firstItem() }} a {{ $ingresos->lastItem() }} de {{ $ingresos->total() }}
            </div>
            <div class="pagination-links">
                <a href="{{ $ingresos->previousPageUrl() ?: '#' }}" class="page-link {{ $ingresos->onFirstPage() ? 'disabled' : '' }}">
                    <i class="fas fa-arrow-left"></i> Anterior
                </a>
                <a href="{{ $ingresos->hasMorePages() ? $ingresos->nextPageUrl() : '#' }}" class="page-link {{ $ingresos->hasMorePages() ? '' : 'disabled' }}">
                    Siguiente <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- ══ MODAL FILTRAR ══ --}}
<div class="modal-overlay" id="filterModal">
    <div class="modal-box-f">
        <div class="mh">
            <span><i class="fas fa-filter" style="margin-right:8px;"></i> Filtrar Datos</span>
            <button onclick="document.getElementById('filterModal').classList.remove('open')">✕</button>
        </div>
        <div class="mb">
            <form method="GET" action="{{ route('admin.proyectos.ingresos', $proyecto) }}">
                <div class="modal-fgrid">
                    <div>
                        <div class="modal-label">Fecha exacta</div>
                        <input type="date" name="fecha" value="{{ $fecha }}" class="modal-input">
                    </div>
                    <div>
                        <div class="modal-label">Tipo de ingreso</div>
                        <select name="tipo_ingreso" class="modal-select">
                            <option value="">Todos</option>
                            @foreach($tipos as $item)
                            <option value="{{ $item }}" @selected($tipo === $item)>{{ str_replace('_', ' ', ucfirst($item)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <div class="modal-label">Desde</div>
                        <input type="date" name="desde" value="{{ $desde }}" class="modal-input">
                    </div>
                    <div>
                        <div class="modal-label">Hasta</div>
                        <input type="date" name="hasta" value="{{ $hasta }}" class="modal-input">
                    </div>
                    <div>
                        <div class="modal-label">Origen</div>
                        <select name="origen" class="modal-select">
                            <option value="">Todos</option>
                            @foreach($origenes as $item)
                            <option value="{{ $item }}" @selected($origen === $item)>{{ ucfirst($item) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <div class="modal-label">Cliente</div>
                        <select name="cliente_id" class="modal-select">
                            <option value="">Todos</option>
                            @foreach($clientes as $cl)
                            <option value="{{ $cl->id }}" @selected((int)$clienteId === (int)$cl->id)>{{ $cl->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <div class="modal-label">Monto mínimo</div>
                        <input type="number" name="monto_min" value="{{ $montoMin }}" step="0.01" min="0" class="modal-input" placeholder="0.00">
                    </div>
                    <div>
                        <div class="modal-label">Monto máximo</div>
                        <input type="number" name="monto_max" value="{{ $montoMax }}" step="0.01" min="0" class="modal-input" placeholder="0.00">
                    </div>
                </div>
                <div style="display:flex;gap:10px;margin-top:18px;justify-content:flex-end;">
                    <a href="{{ route('admin.proyectos.ingresos', $proyecto) }}" class="btn-limpiar-f">
                        <i class="fas fa-xmark"></i> Limpiar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-filter"></i> Aplicar filtros
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ── Tabs ── */
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        const btn = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
        const panel = document.getElementById(`tab-${tabId}`);
        if (btn)   btn.classList.add('active');
        if (panel) panel.classList.add('active');
    }
    window.switchTab = switchTab;

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => switchTab(btn.dataset.tab));
    });

    /* ── Filtrar modal ── */
    document.getElementById('btnFiltrar')?.addEventListener('click', () => {
        document.getElementById('filterModal').classList.add('open');
    });
    document.getElementById('filterModal')?.addEventListener('click', (e) => {
        if (e.target === document.getElementById('filterModal'))
            document.getElementById('filterModal').classList.remove('open');
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') document.getElementById('filterModal')?.classList.remove('open');
    });

    /* ── Datos para gráficas ── */
    const rawDiario  = @json($ingresosPorDia);
    const rawMensual = @json($ingresosPorMes);

    const mesesNombres = ['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    const chartDefaults = {
        borderRadius: 8, borderSkipped: false,
    };

    /* Gráfica diaria (Resumen tab) */
    const ctxD1 = document.getElementById('chartDiario')?.getContext('2d');
    if (ctxD1) {
        new Chart(ctxD1, {
            type: 'bar',
            data: {
                labels: Object.keys(rawDiario).map(d => {
                    const [y,m,day] = d.split('-');
                    return `${day}/${m}`;
                }),
                datasets: [{
                    label: 'Ingresos por día (S/.)',
                    data: Object.values(rawDiario),
                    backgroundColor: 'rgba(37,99,235,.65)',
                    borderColor: '#2563eb',
                    borderWidth: 1.5,
                    ...chartDefaults,
                }],
            },
            options: {
                responsive: true, plugins: { legend: { position: 'top' } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => 'S/. ' + v.toLocaleString() } },
                },
            },
        });
    }

    /* Gráfica diaria (tab Diarios) */
    const ctxD2 = document.getElementById('chartDiario2')?.getContext('2d');
    if (ctxD2) {
        new Chart(ctxD2, {
            type: 'bar',
            data: {
                labels: Object.keys(rawDiario).map(d => { const [y,m,day]=d.split('-'); return `${day}/${m}`; }),
                datasets: [{
                    label: 'S/.',
                    data: Object.values(rawDiario),
                    backgroundColor: 'rgba(6,182,212,.65)',
                    borderColor: '#06b6d4',
                    borderWidth: 1.5,
                    ...chartDefaults,
                }],
            },
            options: {
                responsive: true, plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => 'S/. ' + v.toLocaleString() } } },
            },
        });
    }

    /* Gráfica mensual */
    const ctxM = document.getElementById('chartMensual')?.getContext('2d');
    if (ctxM) {
        new Chart(ctxM, {
            type: 'bar',
            data: {
                labels: Object.keys(rawMensual).map(m => mesesNombres[parseInt(m)] || m),
                datasets: [{
                    label: 'S/.',
                    data: Object.values(rawMensual),
                    backgroundColor: 'rgba(139,92,246,.65)',
                    borderColor: '#8b5cf6',
                    borderWidth: 1.5,
                    ...chartDefaults,
                }],
            },
            options: {
                responsive: true, plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { callback: v => 'S/. ' + v.toLocaleString() } } },
            },
        });
    }
});
</script>
@endpush
