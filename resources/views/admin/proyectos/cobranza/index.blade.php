@extends('layouts.admin-project', ['currentModule' => 'cobranza'])

@section('title', 'Cobranza | ' . $proyecto->nombre)
@section('module_label', 'Cobranza')
@section('page_title', 'Cobranza de ' . $proyecto->nombre)
@section('page_subtitle', 'Busca clientes por DNI o lote, registra pagos operativos y mantén sincronizados el saldo, el cronograma y el estado comercial del proyecto.')

@push('styles')
<style>
    .cobranza-grid{display:grid;grid-template-columns:360px 1fr;gap:22px;align-items:start;}
    .stack-card{padding:22px;}
    .stack-card + .stack-card{margin-top:20px;}
    .filters-stack{display:grid;gap:14px;}
    .mini-label{font-size:11px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);}
    .toolbar-select{width:100%;border:1.5px solid var(--border);background:#fff;border-radius:14px;padding:12px 14px;font:600 13px 'Poppins',sans-serif;color:var(--text);}
    .client-list{display:grid;gap:12px;}
    .client-card{display:block;padding:16px;border-radius:18px;border:1.5px solid var(--border);background:#fff;text-decoration:none;transition:.2s;}
    .client-card:hover{border-color:rgba(85,51,204,.35);transform:translateY(-1px);}
    .client-card.active{border-color:var(--vt);box-shadow:0 14px 28px rgba(85,51,204,.12);}
    .client-card-title{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:10px;}
    .client-name{font-size:14px;font-weight:800;color:var(--text);}
    .client-meta{font-size:12px;color:var(--gray);line-height:1.6;}
    .financial-badge,.lifecycle-badge,.payment-badge,.payment-type{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;}
    .financial-badge::before,.lifecycle-badge::before,.payment-badge::before,.payment-type::before{content:'';width:8px;height:8px;border-radius:50%;}
    .financial-badge.sin_pagos,.lifecycle-badge.anulado,.payment-badge.anulado{background:#fee2e2;color:#b91c1c;}
    .financial-badge.sin_pagos::before,.lifecycle-badge.anulado::before,.payment-badge.anulado::before{background:#dc2626;}
    .financial-badge.reservado,.lifecycle-badge.desistido,.payment-type.reserva{background:#fef3c7;color:#b45309;}
    .financial-badge.reservado::before,.lifecycle-badge.desistido::before,.payment-type.reserva::before{background:#d97706;}
    .financial-badge.financiamiento,.payment-type.inicial,.payment-type.cuota,.payment-type.ajuste_cuota{background:#dbeafe;color:#1d4ed8;}
    .financial-badge.financiamiento::before,.payment-type.inicial::before,.payment-type.cuota::before,.payment-type.ajuste_cuota::before{background:#2563eb;}
    .financial-badge.pagado,.lifecycle-badge.activo,.payment-badge.registrado,.payment-type.contado{background:#dcfce7;color:#15803d;}
    .financial-badge.pagado::before,.lifecycle-badge.activo::before,.payment-badge.registrado::before,.payment-type.contado::before{background:#16a34a;}
    .summary-inline{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:22px;}
    .inline-box{padding:18px;border-radius:18px;border:1px solid var(--border);background:#fff;}
    .inline-box .k{font-size:11px;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);font-weight:800;}
    .inline-box .v{margin-top:8px;font-size:22px;font-weight:900;color:var(--text);}
    .detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-bottom:20px;}
    .detail-card{padding:18px;border-radius:18px;border:1px solid var(--border);background:#fff;}
    .detail-card .label{font-size:11px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);}
    .detail-card .value{margin-top:10px;font-size:22px;font-weight:900;color:var(--text);}
    .detail-card .sub{margin-top:6px;font-size:12px;color:var(--gray);}
    .helper-panel{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;padding:14px 16px;border-radius:16px;background:#f7f8ff;border:1px solid var(--border);font-size:12px;color:var(--gray);}
    .helper-panel strong{color:var(--text);}
    .split-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:18px;}
    .split-head .section-title{margin-bottom:0;}
    .history-table td:last-child{white-space:nowrap;}
    .history-actions{display:flex;gap:8px;flex-wrap:wrap;}
    .btn-small{padding:9px 12px;border-radius:12px;font-size:12px;}
    .empty-mini{padding:26px 18px;border-radius:18px;border:1px dashed var(--border);text-align:center;color:var(--gray);}
    .client-empty{padding:42px 20px;text-align:center;color:var(--gray);}
    .client-empty i{font-size:40px;display:block;margin-bottom:12px;opacity:.4;}
    @media(max-width:1100px){.cobranza-grid{grid-template-columns:1fr;}.summary-inline,.detail-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
    @media(max-width:700px){.summary-inline,.detail-grid,.helper-panel{grid-template-columns:1fr;}}
</style>
@endpush

@section('content')
@php
    $cards = [
        ['key' => 'Total', 'class' => 'is-total', 'icon' => 'fas fa-wallet', 'label' => 'Cartera total'],
        ['key' => 'reservado', 'class' => 'is-reservado', 'icon' => 'fas fa-clock', 'label' => 'Reservados'],
        ['key' => 'financiamiento', 'class' => 'is-financiamiento', 'icon' => 'fas fa-credit-card', 'label' => 'Financiamiento'],
        ['key' => 'pagado', 'class' => 'is-libre', 'icon' => 'fas fa-circle-check', 'label' => 'Pagados'],
        ['key' => 'desistido', 'class' => 'is-vendido', 'icon' => 'fas fa-user-xmark', 'label' => 'Desistidos'],
    ];
@endphp

<section class="summary-grid">
    @foreach($cards as $card)
    <article class="card summary-card {{ $card['class'] }}">
        <div class="summary-icon">
            <i class="{{ $card['icon'] }}"></i>
        </div>
        <div>
            <h3>{{ $resumen[$card['key']] ?? 0 }}</h3>
            <p>{{ $card['label'] }}</p>
        </div>
    </article>
    @endforeach
</section>

<div class="cobranza-grid">
    <div>
        <section class="card stack-card">
            <div class="section-head">
                <div class="section-title">Busqueda de <span>Cobranza</span></div>
            </div>

            <form method="GET" action="{{ route('admin.proyectos.cobranza', $proyecto) }}" class="filters-stack">
                <div>
                    <div class="mini-label">Buscar por DNI</div>
                    <div class="search-box" style="margin-top:8px;">
                        <i class="fas fa-id-card"></i>
                        <input type="text" name="dni" value="{{ $dni }}" placeholder="DNI del cliente">
                    </div>
                </div>

                <div>
                    <div class="mini-label">Buscar por lote</div>
                    <div class="search-box" style="margin-top:8px;">
                        <i class="fas fa-map-location-dot"></i>
                        <input type="text" name="lote" value="{{ $lote }}" placeholder="Manzana, lote o codigo">
                    </div>
                </div>

                <div>
                    <div class="mini-label">Modalidad</div>
                    <select name="modalidad" class="toolbar-select" style="margin-top:8px;">
                        <option value="">Todas</option>
                        @foreach($modalidades as $item)
                        <option value="{{ $item }}" @selected($modalidad === $item)>{{ ucfirst($item) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="mini-label">Estado financiero</div>
                    <select name="estado" class="toolbar-select" style="margin-top:8px;">
                        <option value="">Todos</option>
                        @foreach($estadosCobranza as $item)
                        <option value="{{ $item }}" @selected($estado === $item)>{{ str_replace('_', ' ', ucfirst($item)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-actions" style="margin-top:0;justify-content:flex-start;">
                    <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
                    <a href="{{ route('admin.proyectos.cobranza', $proyecto) }}" class="btn-secondary">Limpiar</a>
                </div>
            </form>
        </section>

        <section class="card stack-card">
            <div class="split-head">
                <div class="section-title">Clientes en <span>Cobranza</span></div>
                <span class="muted">{{ $clientes->total() }} resultados</span>
            </div>

            <div class="client-list">
                @forelse($clientes as $cliente)
                <a
                    href="{{ route('admin.proyectos.cobranza', array_filter(array_merge(['proyecto' => $proyecto], request()->except(['cliente', 'editar_pago', 'clientes_page']), ['cliente' => $cliente->id]), fn ($value) => $value !== null && $value !== '')) }}"
                    class="client-card {{ $selectedClient && $selectedClient->id === $cliente->id ? 'active' : '' }}"
                >
                    <div class="client-card-title">
                        <div>
                            <div class="client-name">{{ $cliente->nombre_completo }}</div>
                            <div class="client-meta">DNI {{ $cliente->dni }}</div>
                        </div>
                        <span class="financial-badge {{ $cliente->estado_cobranza }}">{{ str_replace('_', ' ', ucfirst($cliente->estado_cobranza)) }}</span>
                    </div>
                    <div class="client-meta">
                        <div><strong>Lote:</strong> Mz. {{ $cliente->lote->manzana ?? '-' }} - Lt. {{ $cliente->lote->numero ?? '-' }}</div>
                        <div><strong>Modalidad:</strong> {{ ucfirst($cliente->modalidad) }}</div>
                        <div><strong>Saldo:</strong> S/. {{ number_format((float) $cliente->saldo_pendiente, 2, '.', ',') }}</div>
                    </div>
                </a>
                @empty
                <div class="client-empty">
                    <i class="fas fa-search-dollar"></i>
                    <strong>No hay clientes para cobranza con los filtros actuales.</strong>
                </div>
                @endforelse
            </div>

            @if($clientes->hasPages())
            <div class="pagination">
                <div class="pagination-status">
                    Mostrando {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} clientes
                </div>
                <div class="pagination-links">
                    <a href="{{ $clientes->previousPageUrl() ?: '#' }}" class="page-link {{ $clientes->onFirstPage() ? 'disabled' : '' }}">
                        <i class="fas fa-arrow-left"></i> Anterior
                    </a>
                    <a href="{{ $clientes->hasMorePages() ? $clientes->nextPageUrl() : '#' }}" class="page-link {{ $clientes->hasMorePages() ? '' : 'disabled' }}">
                        Siguiente <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            @endif
        </section>
    </div>

    <div>
        @if($selectedClient)
        <section class="summary-inline">
            <article class="inline-box">
                <div class="k">Cliente</div>
                <div class="v" style="font-size:20px;">{{ $selectedClient->nombre_completo }}</div>
            </article>
            <article class="inline-box">
                <div class="k">Precio del lote</div>
                <div class="v">S/. {{ number_format((float) $selectedClient->precio_lote, 2, '.', ',') }}</div>
            </article>
            <article class="inline-box">
                <div class="k">Total pagado</div>
                <div class="v">S/. {{ number_format((float) $selectedClient->total_pagado, 2, '.', ',') }}</div>
            </article>
            <article class="inline-box">
                <div class="k">Saldo pendiente</div>
                <div class="v">S/. {{ number_format((float) $selectedClient->saldo_pendiente, 2, '.', ',') }}</div>
            </article>
        </section>

        <section class="detail-grid">
            <article class="detail-card">
                <div class="label">Lote asignado</div>
                <div class="value">Mz. {{ $selectedClient->lote->manzana ?? '-' }} - Lt. {{ $selectedClient->lote->numero ?? '-' }}</div>
                <div class="sub">{{ $selectedClient->lote->codigo ?? 'Sin codigo comercial' }}</div>
            </article>
            <article class="detail-card">
                <div class="label">Estado del cliente</div>
                <div class="value" style="font-size:18px;">
                    <span class="financial-badge {{ $selectedClient->estado_cobranza }}">{{ str_replace('_', ' ', ucfirst($selectedClient->estado_cobranza)) }}</span>
                    <span class="lifecycle-badge {{ $selectedClient->estado }}">{{ ucfirst($selectedClient->estado) }}</span>
                </div>
                <div class="sub">Modalidad actual: {{ ucfirst($selectedClient->modalidad) }}</div>
            </article>
            <article class="detail-card">
                <div class="label">Cuota mensual</div>
                <div class="value">S/. {{ number_format((float) $selectedClient->cuota_mensual, 2, '.', ',') }}</div>
                <div class="sub">{{ $selectedClient->numero_cuotas ? $selectedClient->numero_cuotas . ' cuotas configuradas' : 'Sin cronograma configurado' }}</div>
            </article>
            <article class="detail-card">
                <div class="label">Cronograma</div>
                <div class="value" style="font-size:18px;">{{ $selectedClient->cronogramaPagos()->count() }} cuotas</div>
                <div class="sub">
                    <a href="{{ route('admin.proyectos.cobranza.cronograma', [$proyecto, $selectedClient]) }}" class="btn-secondary btn-small" style="margin-top:10px;">
                        <i class="fas fa-calendar-days"></i> Ver cronograma
                    </a>
                </div>
            </article>
        </section>

        <section class="card content-card" style="margin-bottom:22px;">
            <div class="split-head">
                <div class="section-title">{!! $editPayment ? 'Editar <span>Pago</span>' : 'Registrar <span>Pago</span>' !!}</div>
                @if($selectedClient->cronogramaPagos()->exists())
                <form method="POST" action="{{ route('admin.proyectos.cobranza.cronograma.regenerar', [$proyecto, $selectedClient]) }}">
                    @csrf
                    <button type="submit" class="btn-secondary btn-small">
                        <i class="fas fa-rotate"></i> Regenerar cronograma
                    </button>
                </form>
                @endif
            </div>

            @include('admin.proyectos.cobranza._payment-form', [
                'payment' => $editPayment ?: new \App\Models\Pago(),
            ])
        </section>

        <section class="card content-card" style="margin-bottom:22px;">
            <div class="split-head">
                <div class="section-title">Historial de <span>Pagos</span></div>
                <span class="muted">{{ $historialPagos?->total() ?? 0 }} registros</span>
            </div>

            <div class="table-wrap">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Notas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($historialPagos as $pago)
                        <tr>
                            <td>{{ optional($pago->fecha_pago)->format('d/m/Y') }}</td>
                            <td><span class="payment-type {{ $pago->tipo_pago }}">{{ str_replace('_', ' ', ucfirst($pago->tipo_pago)) }}</span></td>
                            <td class="cell-strong">S/. {{ number_format((float) $pago->monto, 2, '.', ',') }}</td>
                            <td><span class="payment-badge {{ $pago->estado_pago }}">{{ ucfirst($pago->estado_pago) }}</span></td>
                            <td class="muted">{{ $pago->notas ?: 'Sin notas' }}</td>
                            <td>
                                <div class="history-actions">
                                    @if($pago->estado_pago === 'registrado')
                                    <a
                                        href="{{ route('admin.proyectos.cobranza', array_filter(array_merge(['proyecto' => $proyecto], request()->except(['editar_pago']), ['cliente' => $selectedClient->id, 'editar_pago' => $pago->id]), fn ($value) => $value !== null && $value !== '')) }}"
                                        class="btn-secondary btn-small"
                                    >
                                        <i class="fas fa-pen"></i> Editar
                                    </a>
                                    <form method="POST" action="{{ route('admin.proyectos.cobranza.pagos.destroy', [$proyecto, $pago]) }}" onsubmit="return confirm('Se anulara este pago y se recalcularan los saldos del cliente.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary btn-small">
                                            <i class="fas fa-trash"></i> Anular
                                        </button>
                                    </form>
                                    @else
                                    <span class="muted">Sin acciones</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-receipt"></i>
                                    <strong>Este cliente aun no tiene pagos registrados.</strong>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($historialPagos && $historialPagos->hasPages())
            <div class="pagination">
                <div class="pagination-status">
                    Mostrando {{ $historialPagos->firstItem() }} a {{ $historialPagos->lastItem() }} de {{ $historialPagos->total() }} pagos
                </div>
                <div class="pagination-links">
                    <a href="{{ $historialPagos->previousPageUrl() ?: '#' }}" class="page-link {{ $historialPagos->onFirstPage() ? 'disabled' : '' }}">
                        <i class="fas fa-arrow-left"></i> Anterior
                    </a>
                    <a href="{{ $historialPagos->hasMorePages() ? $historialPagos->nextPageUrl() : '#' }}" class="page-link {{ $historialPagos->hasMorePages() ? '' : 'disabled' }}">
                        Siguiente <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            @endif
        </section>

        <section class="card content-card">
            <div class="split-head">
                <div class="section-title">Preview de <span>Cronograma</span></div>
                <a href="{{ route('admin.proyectos.cobranza.cronograma', [$proyecto, $selectedClient]) }}" class="btn-secondary btn-small">
                    <i class="fas fa-up-right-from-square"></i> Abrir completo
                </a>
            </div>

            @if($cronogramaPreview->isNotEmpty())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Cuota</th>
                            <th>Vencimiento</th>
                            <th>Monto</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cronogramaPreview as $cuota)
                        <tr>
                            <td>#{{ $cuota->numero_cuota }}</td>
                            <td>{{ optional($cuota->fecha_vencimiento)->format('d/m/Y') }}</td>
                            <td>S/. {{ number_format((float) $cuota->monto, 2, '.', ',') }}</td>
                            <td><span class="financial-badge {{ $cuota->estado === 'pendiente' ? 'reservado' : ($cuota->estado === 'vencido' ? 'sin_pagos' : 'pagado') }}">{{ ucfirst($cuota->estado) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-mini">
                El cliente no tiene cronograma vigente. Se generara automaticamente cuando entre a financiamiento con cuotas configuradas.
            </div>
            @endif
        </section>
        @else
        <section class="card content-card">
            <div class="client-empty">
                <i class="fas fa-user-large-slash"></i>
                <strong>No hay un cliente seleccionado.</strong>
                <div style="margin-top:6px;">Filtra la cartera y elige un cliente para registrar su cobranza.</div>
            </div>
        </section>
        @endif
    </div>
</div>
@endsection
