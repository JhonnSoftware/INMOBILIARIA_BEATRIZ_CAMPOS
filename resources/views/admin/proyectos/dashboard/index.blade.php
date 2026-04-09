@extends('layouts.admin-project', ['currentModule' => 'dashboard'])

@section('title', 'Dashboard | ' . $proyecto->nombre)
@section('module_label', 'Dashboard')
@section('page_title', 'Dashboard de ' . $proyecto->nombre)
@section('page_subtitle', 'Resumen ejecutivo y operativo del proyecto con lotes, clientes, cobranza, finanzas, actividad reciente y accesos directos a todos los modulos del sistema.')

@push('styles')
<style>
    .shortcut-grid,.action-grid,.summary-grid-dashboard,.chart-grid,.recent-grid{display:grid;gap:16px;}
    .shortcut-grid{grid-template-columns:repeat(7,minmax(0,1fr));margin-bottom:22px;}
    .shortcut-card{display:flex;align-items:center;gap:12px;padding:16px 18px;border-radius:18px;background:#fff;border:1px solid var(--border);text-decoration:none;color:var(--text);box-shadow:0 10px 28px rgba(15,23,42,.05);transition:.2s;}
    .shortcut-card:hover{transform:translateY(-2px);border-color:rgba(85,51,204,.24);}
    .shortcut-icon{width:42px;height:42px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,rgba(238,0,187,.14),rgba(85,51,204,.16));color:var(--vt);font-size:18px;flex-shrink:0;}
    .shortcut-label{font-size:13px;font-weight:800;line-height:1.25;}
    .shortcut-helper{font-size:11px;color:var(--gray);margin-top:2px;}
    .action-grid{grid-template-columns:repeat(6,minmax(0,1fr));margin-bottom:22px;}
    .action-card{padding:16px;border-radius:18px;background:linear-gradient(135deg,#ffffff 0%,#faf8ff 100%);border:1px solid rgba(85,51,204,.12);text-decoration:none;color:var(--text);box-shadow:0 10px 24px rgba(85,51,204,.08);}
    .action-card i{font-size:18px;color:var(--mg);margin-bottom:10px;display:block;}
    .action-card strong{display:block;font-size:13px;font-weight:800;line-height:1.3;}
    .action-card span{display:block;margin-top:4px;font-size:11px;color:var(--gray);line-height:1.4;}
    .summary-grid-dashboard{grid-template-columns:repeat(5,minmax(0,1fr));margin-bottom:22px;}
    .metric-card{padding:20px;border-radius:20px;background:#fff;border:1px solid var(--border);box-shadow:0 10px 30px rgba(15,23,42,.05);}
    .metric-label{font-size:11px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);}
    .metric-value{margin-top:10px;font-size:30px;font-weight:900;line-height:1;color:var(--text);}
    .metric-helper{margin-top:6px;font-size:12px;color:var(--gray);line-height:1.5;}
    .metric-card.is-green .metric-value{color:#15803d;}
    .metric-card.is-yellow .metric-value{color:#b45309;}
    .metric-card.is-blue .metric-value{color:#1d4ed8;}
    .metric-card.is-red .metric-value{color:#b91c1c;}
    .metric-card.is-purple .metric-value{color:var(--vt);}
    .section-grid{display:grid;grid-template-columns:1.25fr .95fr;gap:18px;margin-bottom:22px;}
    .block-card{padding:22px;}
    .block-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px;}
    .block-title{font-size:17px;font-weight:800;color:var(--text);}
    .block-title span{color:var(--mg);}
    .document-tags{display:flex;gap:8px;flex-wrap:wrap;}
    .document-tag{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:999px;background:#f6f5ff;border:1px solid rgba(85,51,204,.12);font-size:12px;font-weight:700;color:var(--vt);}
    .document-tag strong{color:var(--text);}
    .alert-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;}
    .alert-card{padding:18px;border-radius:18px;background:var(--bg);border:1px solid var(--border);}
    .alert-label{font-size:11px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);}
    .alert-value{margin-top:8px;font-size:26px;font-weight:900;line-height:1;color:var(--text);}
    .alert-card.is-warning .alert-value{color:#d97706;}
    .alert-card.is-danger .alert-value{color:#dc2626;}
    .alert-card.is-success .alert-value{color:#15803d;}
    .alert-card.is-blue .alert-value{color:#2563eb;}
    .alert-card.is-purple .alert-value{color:#6d28d9;}
    .alert-card.is-dark .alert-value{color:#1a1a2e;}
    .latest-payment{padding:18px;border-radius:18px;background:linear-gradient(135deg,#1a1a2e 0%,#2d1b69 100%);color:#fff;}
    .latest-payment small{display:block;font-size:11px;letter-spacing:.8px;text-transform:uppercase;color:rgba(255,255,255,.62);margin-bottom:8px;}
    .latest-payment strong{display:block;font-size:18px;line-height:1.4;}
    .latest-payment span{display:block;margin-top:4px;font-size:12px;color:rgba(255,255,255,.72);}
    .chart-grid{grid-template-columns:repeat(2,minmax(0,1fr));margin-bottom:22px;}
    .chart-card{padding:22px;}
    .chart-title{font-size:16px;font-weight:800;color:var(--text);}
    .chart-subtitle{margin-top:4px;margin-bottom:16px;font-size:12px;color:var(--gray);}
    .chart-box{position:relative;min-height:290px;}
    .chart-box canvas{width:100% !important;height:290px !important;}
    .chart-empty{display:flex;align-items:center;justify-content:center;min-height:240px;text-align:center;color:var(--gray);font-size:13px;line-height:1.6;}
    .recent-grid{grid-template-columns:repeat(2,minmax(0,1fr));}
    .recent-card{padding:22px;}
    .mini-table{width:100%;border-collapse:collapse;}
    .mini-table th{padding:0 0 10px;border-bottom:1px solid var(--border);text-align:left;font-size:11px;font-weight:800;color:var(--gray);text-transform:uppercase;letter-spacing:.7px;}
    .mini-table td{padding:12px 0;border-bottom:1px solid var(--border);font-size:12px;vertical-align:top;}
    .mini-table tr:last-child td{border-bottom:none;}
    .mini-main{font-weight:700;color:var(--text);line-height:1.45;}
    .mini-sub{margin-top:3px;color:var(--gray);line-height:1.45;}
    .mini-money{font-weight:800;color:var(--vt);white-space:nowrap;}
    .empty-mini{padding:18px 0;color:var(--gray);font-size:13px;text-align:center;}
    .pill-state{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;font-size:11px;font-weight:800;}
    .pill-state::before{content:'';width:8px;height:8px;border-radius:50%;}
    .pill-state.activo{background:#dcfce7;color:#15803d;}.pill-state.activo::before{background:#16a34a;}
    .pill-state.reservado{background:#fef3c7;color:#b45309;}.pill-state.reservado::before{background:#d97706;}
    .pill-state.financiamiento{background:#dbeafe;color:#1d4ed8;}.pill-state.financiamiento::before{background:#2563eb;}
    .pill-state.pagado{background:#ede9fe;color:#6d28d9;}.pill-state.pagado::before{background:#7c3aed;}
    .pill-state.eliminado{background:#fee2e2;color:#b91c1c;}.pill-state.eliminado::before{background:#dc2626;}
    .pill-state.proyecto{background:#f3e8ff;color:#6d28d9;}.pill-state.proyecto::before{background:#7c3aed;}
    .pill-state.lote{background:#dbeafe;color:#1d4ed8;}.pill-state.lote::before{background:#2563eb;}
    .pill-state.cliente{background:#dcfce7;color:#15803d;}.pill-state.cliente::before{background:#16a34a;}
    @media(max-width:1280px){.shortcut-grid{grid-template-columns:repeat(4,minmax(0,1fr));}.action-grid{grid-template-columns:repeat(3,minmax(0,1fr));}.summary-grid-dashboard{grid-template-columns:repeat(3,minmax(0,1fr));}.section-grid{grid-template-columns:1fr;}.chart-grid,.recent-grid{grid-template-columns:1fr;}}
    @media(max-width:880px){.shortcut-grid,.action-grid,.summary-grid-dashboard,.alert-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
    @media(max-width:640px){.shortcut-grid,.action-grid,.summary-grid-dashboard,.alert-grid{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')
@php
    $moduleLinks = [
        ['label' => 'Lotes', 'helper' => 'Inventario y estados', 'route' => route('admin.proyectos.lotes', $proyecto), 'icon' => 'fas fa-map'],
        ['label' => 'Clientes', 'helper' => 'Prospectos y ventas', 'route' => route('admin.proyectos.clientes', $proyecto), 'icon' => 'fas fa-users'],
        ['label' => 'Cobranza', 'helper' => 'Pagos y cronogramas', 'route' => route('admin.proyectos.cobranza', $proyecto), 'icon' => 'fas fa-hand-holding-dollar'],
        ['label' => 'Ingresos', 'helper' => 'Entradas del proyecto', 'route' => route('admin.proyectos.ingresos', $proyecto), 'icon' => 'fas fa-chart-pie'],
        ['label' => 'Egresos', 'helper' => 'Gastos y adjuntos', 'route' => route('admin.proyectos.egresos', $proyecto), 'icon' => 'fas fa-receipt'],
        ['label' => 'Caja', 'helper' => 'Flujo consolidado', 'route' => route('admin.proyectos.caja', $proyecto), 'icon' => 'fas fa-cash-register'],
        ['label' => 'Documentos', 'helper' => 'Repositorio legal', 'route' => route('admin.proyectos.documentos', $proyecto), 'icon' => 'fas fa-folder-open'],
    ];

    $quickActions = [
        ['label' => 'Nuevo lote', 'helper' => 'Registrar disponibilidad', 'route' => route('admin.proyectos.lotes.create', $proyecto), 'icon' => 'fas fa-plus'],
        ['label' => 'Nuevo cliente', 'helper' => 'Alta comercial', 'route' => route('admin.proyectos.clientes.create', $proyecto), 'icon' => 'fas fa-user-plus'],
        ['label' => 'Registrar pago', 'helper' => 'Entrar a cobranza', 'route' => route('admin.proyectos.cobranza', $proyecto), 'icon' => 'fas fa-money-bill-wave'],
        ['label' => 'Registrar ingreso', 'helper' => 'Ingreso manual', 'route' => route('admin.proyectos.ingresos.create', $proyecto), 'icon' => 'fas fa-sack-dollar'],
        ['label' => 'Registrar egreso', 'helper' => 'Salida del proyecto', 'route' => route('admin.proyectos.egresos.create', $proyecto), 'icon' => 'fas fa-file-invoice-dollar'],
        ['label' => 'Subir documento', 'helper' => 'Contrato, voucher o plano', 'route' => route('admin.proyectos.documentos.create', $proyecto), 'icon' => 'fas fa-upload'],
    ];

    $lotCards = [
        ['label' => 'Total lotes', 'value' => $dashboard['lotes']['total_lotes'], 'class' => 'is-purple', 'helper' => 'Inventario del proyecto'],
        ['label' => 'Lotes libres', 'value' => $dashboard['lotes']['lotes_libres'], 'class' => 'is-green', 'helper' => 'Disponibles para venta'],
        ['label' => 'Lotes reservados', 'value' => $dashboard['lotes']['lotes_reservados'], 'class' => 'is-yellow', 'helper' => 'Separados'],
        ['label' => 'En financiamiento', 'value' => $dashboard['lotes']['lotes_financiamiento'], 'class' => 'is-blue', 'helper' => 'Con plan activo'],
        ['label' => 'Lotes vendidos', 'value' => $dashboard['lotes']['lotes_vendidos'], 'class' => 'is-red', 'helper' => 'Cerrados'],
    ];

    $clientCards = [
        ['label' => 'Total clientes', 'value' => $dashboard['clientes']['total_clientes'], 'class' => 'is-purple', 'helper' => 'Historial del proyecto'],
        ['label' => 'Clientes activos', 'value' => $dashboard['clientes']['clientes_activos'], 'class' => 'is-green', 'helper' => 'Con expediente activo'],
        ['label' => 'En reserva', 'value' => $dashboard['clientes']['clientes_reserva'], 'class' => 'is-yellow', 'helper' => 'Estado de cobranza'],
        ['label' => 'En financiamiento', 'value' => $dashboard['clientes']['clientes_financiamiento'], 'class' => 'is-blue', 'helper' => 'Con cronograma'],
        ['label' => 'Pagados / cerrados', 'value' => $dashboard['clientes']['clientes_pagados'], 'class' => 'is-green', 'helper' => 'Operacion completada'],
        ['label' => 'Desistidos / anulados', 'value' => $dashboard['clientes']['clientes_desistidos'], 'class' => 'is-red', 'helper' => 'Casos cerrados sin continuidad'],
    ];

    $financialCards = [
        ['label' => 'Total cobrado', 'value' => 'S/. ' . number_format((float) $dashboard['financial']['total_cobrado_historico'], 2, '.', ','), 'class' => 'is-green', 'helper' => 'Historico del proyecto'],
        ['label' => 'Saldo pendiente', 'value' => 'S/. ' . number_format((float) $dashboard['financial']['saldo_pendiente_total'], 2, '.', ','), 'class' => 'is-yellow', 'helper' => 'Clientes activos'],
        ['label' => 'Ingresos del mes', 'value' => 'S/. ' . number_format((float) $dashboard['financial']['ingresos_mes'], 2, '.', ','), 'class' => 'is-green', 'helper' => 'Mes actual'],
        ['label' => 'Egresos del mes', 'value' => 'S/. ' . number_format((float) $dashboard['financial']['egresos_mes'], 2, '.', ','), 'class' => 'is-red', 'helper' => 'Mes actual'],
        ['label' => 'Flujo neto del mes', 'value' => 'S/. ' . number_format((float) $dashboard['financial']['flujo_neto_mes'], 2, '.', ','), 'class' => ((float) $dashboard['financial']['flujo_neto_mes']) >= 0 ? 'is-blue' : 'is-red', 'helper' => 'Ingresos menos egresos'],
        ['label' => 'Ingresos hoy', 'value' => 'S/. ' . number_format((float) $dashboard['financial']['ingresos_hoy'], 2, '.', ','), 'class' => 'is-green', 'helper' => 'Corte diario'],
        ['label' => 'Egresos hoy', 'value' => 'S/. ' . number_format((float) $dashboard['financial']['egresos_hoy'], 2, '.', ','), 'class' => 'is-red', 'helper' => 'Corte diario'],
    ];
@endphp

<section class="shortcut-grid">
    @foreach($moduleLinks as $item)
    <a href="{{ $item['route'] }}" class="shortcut-card">
        <div class="shortcut-icon"><i class="{{ $item['icon'] }}"></i></div>
        <div>
            <div class="shortcut-label">{{ $item['label'] }}</div>
            <div class="shortcut-helper">{{ $item['helper'] }}</div>
        </div>
    </a>
    @endforeach
</section>

<section class="action-grid">
    @foreach($quickActions as $item)
    <a href="{{ $item['route'] }}" class="action-card">
        <i class="{{ $item['icon'] }}"></i>
        <strong>{{ $item['label'] }}</strong>
        <span>{{ $item['helper'] }}</span>
    </a>
    @endforeach
</section>

<section class="card block-card" style="margin-bottom:22px;">
    <div class="block-head">
        <div class="block-title">Vista general del <span>Proyecto</span></div>
        <span class="pill-state {{ $dashboard['project']['estado'] === 'activo' ? 'activo' : 'eliminado' }}">{{ ucfirst($dashboard['project']['estado']) }}</span>
    </div>
    <div class="metric-helper" style="font-size:13px;color:var(--text);line-height:1.7;">
        <strong>Ubicacion:</strong> {{ $dashboard['project']['ubicacion'] }}<br>
        @if($dashboard['project']['descripcion'])
        <strong>Descripcion:</strong> {{ $dashboard['project']['descripcion'] }}<br>
        @endif
        @if($dashboard['project']['fecha_inicio'])
        <strong>Fecha de inicio:</strong> {{ optional($dashboard['project']['fecha_inicio'])->format('d/m/Y') }}
        @endif
        @if($dashboard['project']['fecha_lanzamiento'])
        <br><strong>Fecha de lanzamiento:</strong> {{ optional($dashboard['project']['fecha_lanzamiento'])->format('d/m/Y') }}
        @endif
    </div>
</section>

<section class="summary-grid-dashboard">
    @foreach($lotCards as $card)
    <article class="metric-card {{ $card['class'] }}">
        <div class="metric-label">{{ $card['label'] }}</div>
        <div class="metric-value">{{ number_format((float) $card['value']) }}</div>
        <div class="metric-helper">{{ $card['helper'] }}</div>
    </article>
    @endforeach
</section>

<section class="summary-grid-dashboard">
    @foreach($clientCards as $card)
    <article class="metric-card {{ $card['class'] }}">
        <div class="metric-label">{{ $card['label'] }}</div>
        <div class="metric-value">{{ number_format((float) $card['value']) }}</div>
        <div class="metric-helper">{{ $card['helper'] }}</div>
    </article>
    @endforeach
</section>

<section class="summary-grid-dashboard">
    @foreach($financialCards as $card)
    <article class="metric-card {{ $card['class'] }}">
        <div class="metric-label">{{ $card['label'] }}</div>
        <div class="metric-value" style="font-size:26px;">{{ $card['value'] }}</div>
        <div class="metric-helper">{{ $card['helper'] }}</div>
    </article>
    @endforeach
</section>

<section class="section-grid">
    <article class="card block-card">
        <div class="block-head">
            <div class="block-title">Cobranza y <span>Alertas</span></div>
            <a href="{{ route('admin.proyectos.cobranza', $proyecto) }}" class="btn-secondary">Ir a cobranza</a>
        </div>

        <div class="alert-grid">
            <div class="alert-card is-blue">
                <div class="alert-label">Cuotas pendientes</div>
                <div class="alert-value">{{ number_format((int) $dashboard['cobranza']['cuotas_pendientes']) }}</div>
            </div>
            <div class="alert-card is-danger">
                <div class="alert-label">Cuotas vencidas</div>
                <div class="alert-value">{{ number_format((int) $dashboard['cobranza']['cuotas_vencidas']) }}</div>
            </div>
            <div class="alert-card is-warning">
                <div class="alert-label">Clientes con saldo</div>
                <div class="alert-value">{{ number_format((int) $dashboard['cobranza']['clientes_con_saldo']) }}</div>
            </div>
            <div class="alert-card is-success">
                <div class="alert-label">Cobrado del mes</div>
                <div class="alert-value" style="font-size:22px;">S/. {{ number_format((float) $dashboard['cobranza']['total_pagado_mes'], 2, '.', ',') }}</div>
            </div>
            <div class="alert-card is-purple">
                <div class="alert-label">Pagos del mes</div>
                <div class="alert-value">{{ number_format((int) $dashboard['cobranza']['cantidad_pagos_mes']) }}</div>
            </div>
            <div class="alert-card is-dark">
                <div class="alert-label">Documentos activos</div>
                <div class="alert-value">{{ number_format((int) $dashboard['documents']['documentos_total']) }}</div>
            </div>
        </div>

        <div style="margin-top:18px;">
            @if($dashboard['cobranza']['ultimo_pago'])
            <div class="latest-payment">
                <small>Ultimo pago registrado</small>
                <strong>
                    {{ optional($dashboard['cobranza']['ultimo_pago']->fecha_pago)->format('d/m/Y') }}
                    - S/. {{ number_format((float) $dashboard['cobranza']['ultimo_pago']->monto, 2, '.', ',') }}
                </strong>
                <span>
                    {{ $dashboard['cobranza']['ultimo_pago']->cliente?->nombre_completo ?: 'Cliente no disponible' }}
                    @if($dashboard['cobranza']['ultimo_pago']->lote)
                    - Mz. {{ $dashboard['cobranza']['ultimo_pago']->lote->manzana }} Lt. {{ $dashboard['cobranza']['ultimo_pago']->lote->numero }}
                    @endif
                </span>
                <span>Tipo: {{ ucfirst($dashboard['cobranza']['ultimo_pago']->tipo_pago) }}</span>
            </div>
            @else
            <div class="chart-empty">Aun no hay pagos registrados en este proyecto.</div>
            @endif
        </div>
    </article>

    <article class="card block-card">
        <div class="block-head">
            <div class="block-title">Documentos del <span>Proyecto</span></div>
            <a href="{{ route('admin.proyectos.documentos', $proyecto) }}" class="btn-secondary">Ver documentos</a>
        </div>

        <div class="metric-card is-purple" style="box-shadow:none;margin-bottom:16px;">
            <div class="metric-label">Total documentos activos</div>
            <div class="metric-value">{{ number_format((int) $dashboard['documents']['documentos_total']) }}</div>
            <div class="metric-helper">Contratos, vouchers, planos y anexos centralizados en una sola tabla.</div>
        </div>

        <div class="document-tags">
            @forelse(array_slice($dashboard['documents']['por_tipo'], 0, 8) as $item)
            <span class="document-tag"><strong>{{ ucfirst(str_replace('_', ' ', $item['tipo'])) }}</strong> {{ $item['total'] }}</span>
            @empty
            <span class="document-tag"><strong>Sin documentos</strong> 0</span>
            @endforelse
        </div>
    </article>
</section>

<section class="chart-grid">
    <article class="card chart-card">
        <div class="chart-title">Estado de lotes</div>
        <div class="chart-subtitle">Distribucion actual del inventario comercial del proyecto.</div>
        <div class="chart-box">
            <canvas id="lotsStatusChart"></canvas>
        </div>
    </article>

    <article class="card chart-card">
        <div class="chart-title">Cuotas pagadas vs pendientes</div>
        <div class="chart-subtitle">Lectura rapida del cronograma consolidado del proyecto.</div>
        <div class="chart-box">
            <canvas id="installmentsChart"></canvas>
        </div>
    </article>

    <article class="card chart-card">
        <div class="chart-title">Ingresos vs egresos por mes</div>
        <div class="chart-subtitle">Comparativo financiero mensual de los ultimos seis meses.</div>
        <div class="chart-box">
            <canvas id="cashMonthlyChart"></canvas>
        </div>
    </article>

    <article class="card chart-card">
        <div class="chart-title">Cobranza por periodo</div>
        <div class="chart-subtitle">Monto cobrado por mes usando pagos validos del proyecto.</div>
        <div class="chart-box">
            <canvas id="collectionsChart"></canvas>
        </div>
    </article>
</section>

<section class="recent-grid">
    <article class="card recent-card">
        <div class="block-head">
            <div class="block-title">Ultimos <span>Pagos</span></div>
        </div>
        <table class="mini-table">
            <thead>
                <tr>
                    <th>Operacion</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dashboard['recents']['pagos'] as $pago)
                <tr>
                    <td>
                        <div class="mini-main">{{ $pago->cliente?->nombre_completo ?: 'Cliente no disponible' }}</div>
                        <div class="mini-sub">
                            {{ optional($pago->fecha_pago)->format('d/m/Y') }} - {{ ucfirst($pago->tipo_pago) }}
                            @if($pago->lote) - Mz. {{ $pago->lote->manzana }} Lt. {{ $pago->lote->numero }} @endif
                        </div>
                    </td>
                    <td class="mini-money">S/. {{ number_format((float) $pago->monto, 2, '.', ',') }}</td>
                </tr>
                @empty
                <tr><td colspan="2"><div class="empty-mini">Sin pagos recientes.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </article>

    <article class="card recent-card">
        <div class="block-head">
            <div class="block-title">Ultimos <span>Ingresos</span></div>
        </div>
        <table class="mini-table">
            <thead>
                <tr>
                    <th>Concepto</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dashboard['recents']['ingresos'] as $ingreso)
                <tr>
                    <td>
                        <div class="mini-main">{{ $ingreso->concepto }}</div>
                        <div class="mini-sub">{{ optional($ingreso->fecha_ingreso)->format('d/m/Y') }} @if($ingreso->cliente) - {{ $ingreso->cliente->nombre_completo }} @endif</div>
                    </td>
                    <td class="mini-money">S/. {{ number_format((float) $ingreso->monto, 2, '.', ',') }}</td>
                </tr>
                @empty
                <tr><td colspan="2"><div class="empty-mini">Sin ingresos recientes.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </article>

    <article class="card recent-card">
        <div class="block-head">
            <div class="block-title">Ultimos <span>Egresos</span></div>
        </div>
        <table class="mini-table">
            <thead>
                <tr>
                    <th>Descripcion</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dashboard['recents']['egresos'] as $egreso)
                <tr>
                    <td>
                        <div class="mini-main">{{ $egreso->descripcion ?: $egreso->categoria }}</div>
                        <div class="mini-sub">{{ optional($egreso->fecha)->format('d/m/Y') }} - {{ $egreso->categoria_principal }} / {{ $egreso->categoria }}</div>
                    </td>
                    <td class="mini-money">S/. {{ number_format((float) $egreso->monto, 2, '.', ',') }}</td>
                </tr>
                @empty
                <tr><td colspan="2"><div class="empty-mini">Sin egresos recientes.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </article>

    <article class="card recent-card">
        <div class="block-head">
            <div class="block-title">Ultimos <span>Documentos</span></div>
        </div>
        <table class="mini-table">
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Contexto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dashboard['recents']['documentos'] as $documento)
                <tr>
                    <td>
                        <div class="mini-main">{{ $documento->titulo }}</div>
                        <div class="mini-sub">{{ $documento->nombre_original }} - {{ optional($documento->created_at)->format('d/m/Y H:i') }}</div>
                    </td>
                    <td>
                        <span class="pill-state {{ $documento->contexto === 'operacion' ? 'pagado' : $documento->contexto }}">{{ ucfirst($documento->contexto) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="2"><div class="empty-mini">Sin documentos recientes.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </article>

    <article class="card recent-card">
        <div class="block-head">
            <div class="block-title">Ultimos <span>Clientes</span></div>
        </div>
        <table class="mini-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dashboard['recents']['clientes'] as $cliente)
                <tr>
                    <td>
                        <div class="mini-main">{{ $cliente->nombre_completo }}</div>
                        <div class="mini-sub">{{ $cliente->dni }} @if($cliente->lote) - Mz. {{ $cliente->lote->manzana }} Lt. {{ $cliente->lote->numero }} @endif</div>
                    </td>
                    <td>
                        <span class="pill-state {{ $cliente->estado_cobranza === 'sin_pagos' ? 'eliminado' : $cliente->estado_cobranza }}">{{ ucfirst(str_replace('_', ' ', $cliente->estado_cobranza)) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="2"><div class="empty-mini">Sin clientes recientes.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </article>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof Chart === 'undefined') {
            return;
        }

        const money = (value) => `S/. ${Number(value || 0).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

        const lotsCtx = document.getElementById('lotsStatusChart');
        if (lotsCtx) {
            new Chart(lotsCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($dashboard['charts']['lotes_estado']['labels']),
                    datasets: [{
                        data: @json($dashboard['charts']['lotes_estado']['data']),
                        backgroundColor: ['#16a34a', '#d97706', '#2563eb', '#dc2626'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        const installmentsCtx = document.getElementById('installmentsChart');
        if (installmentsCtx) {
            new Chart(installmentsCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($dashboard['charts']['cuotas_estado']['labels']),
                    datasets: [{
                        data: @json($dashboard['charts']['cuotas_estado']['data']),
                        backgroundColor: ['#16a34a', '#2563eb', '#dc2626'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        const cashCtx = document.getElementById('cashMonthlyChart');
        if (cashCtx) {
            new Chart(cashCtx, {
                type: 'bar',
                data: {
                    labels: @json($dashboard['charts']['ingresos_egresos_mensual']['labels']),
                    datasets: [
                        {
                            label: 'Ingresos',
                            data: @json($dashboard['charts']['ingresos_egresos_mensual']['ingresos']),
                            backgroundColor: 'rgba(22, 163, 74, 0.78)',
                            borderRadius: 8,
                        },
                        {
                            label: 'Egresos',
                            data: @json($dashboard['charts']['ingresos_egresos_mensual']['egresos']),
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
                                label: (ctx) => `${ctx.dataset.label}: ${money(ctx.parsed.y)}`,
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: (value) => money(value) }
                        }
                    }
                }
            });
        }

        const collectionsCtx = document.getElementById('collectionsChart');
        if (collectionsCtx) {
            new Chart(collectionsCtx, {
                type: 'line',
                data: {
                    labels: @json($dashboard['charts']['cobranza_periodo']['labels']),
                    datasets: [{
                        label: 'Cobranza',
                        data: @json($dashboard['charts']['cobranza_periodo']['data']),
                        borderColor: '#5533CC',
                        backgroundColor: 'rgba(85, 51, 204, 0.14)',
                        fill: true,
                        tension: 0.35,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.dataset.label}: ${money(ctx.parsed.y)}`,
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: (value) => money(value) }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
