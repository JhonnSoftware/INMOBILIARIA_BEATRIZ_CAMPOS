@extends('layouts.admin-project', ['currentModule' => 'cobranza'])

@section('title', 'Cobranza | ' . $proyecto->nombre)
@section('module_label', 'Cobranza')
@section('page_title', 'Cobranza de ' . $proyecto->nombre)
@section('page_subtitle', 'Busca clientes por DNI o lote, registra pagos operativos y mantén sincronizados el saldo, el cronograma y el estado comercial del proyecto.')

@push('styles')
<style>
    /* ── Tabs ── */
    .cob-tabs{display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:28px;}
    .cob-tab{padding:13px 22px;font:700 14px 'Poppins',sans-serif;color:var(--gray);cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px;text-decoration:none;transition:.2s;display:flex;align-items:center;gap:8px;background:none;border-top:none;border-left:none;border-right:none;}
    .cob-tab:hover{color:var(--vt);}
    .cob-tab.active{color:var(--vt);border-bottom-color:var(--vt);}

    /* ── Search card ── */
    .search-hero{border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(85,51,204,.10);margin-bottom:24px;}
    .search-hero-head{background:linear-gradient(135deg,#4f46e5 0%,#6366f1 50%,#4338ca 100%);padding:26px 32px;position:relative;overflow:hidden;}
    .search-hero-head::before{content:'';position:absolute;right:-60px;top:-60px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.08);}
    .search-hero-head::after{content:'';position:absolute;right:40px;bottom:-80px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.05);}
    .search-hero-head h2{font-size:22px;font-weight:900;color:#fff;margin:0;position:relative;z-index:1;}
    .search-hero-body{background:#fff;padding:28px 32px;}

    /* ── Toggle mode ── */
    .mode-toggle{display:flex;align-items:center;gap:14px;margin-bottom:22px;}
    .mode-btn{display:inline-flex;align-items:center;gap:7px;padding:9px 20px;border-radius:999px;font:700 13px 'Poppins',sans-serif;cursor:pointer;border:2px solid transparent;transition:.2s;text-decoration:none;}
    .mode-btn.is-active{background:var(--vt);color:#fff;border-color:var(--vt);}
    .mode-btn.is-inactive{background:transparent;color:var(--vt);border-color:transparent;}
    .mode-btn.is-inactive:hover{background:#f0eeff;}

    /* ── Search row ── */
    .search-row{display:flex;gap:14px;align-items:flex-end;}
    .search-fields{flex:1;display:grid;gap:14px;}
    .search-fields.two-col{grid-template-columns:1fr 1fr;}
    .search-field-wrap label{display:block;font-size:12px;font-weight:800;letter-spacing:.7px;text-transform:uppercase;color:var(--gray);margin-bottom:8px;}
    .search-input{width:100%;border:1.5px solid #e0e0ef;background:#f8f8ff;border-radius:14px;padding:14px 18px;font:600 14px 'Poppins',sans-serif;color:var(--text);outline:none;transition:.2s;box-sizing:border-box;}
    .search-input:focus{border-color:var(--vt);background:#fff;box-shadow:0 0 0 4px rgba(85,51,204,.08);}
    .search-input::placeholder{color:#b0b0c8;font-weight:500;}
    .btn-search{flex-shrink:0;padding:14px 36px;border-radius:14px;background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;border:none;font:800 13px 'Poppins',sans-serif;letter-spacing:.5px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;transition:.2s;white-space:nowrap;}
    .btn-search:hover{background:linear-gradient(135deg,#4338ca,#4f46e5);transform:translateY(-1px);box-shadow:0 6px 18px rgba(79,70,229,.3);}

    /* ── Summary inline ── */
    .summary-inline{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:22px;}
    .inline-box{padding:18px;border-radius:18px;border:1px solid var(--border);background:#fff;}
    .inline-box .k{font-size:11px;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);font-weight:800;}
    .inline-box .v{margin-top:8px;font-size:22px;font-weight:900;color:var(--text);}

    /* ── Detail grid ── */
    .detail-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;margin-bottom:20px;}
    .detail-card{padding:18px;border-radius:18px;border:1px solid var(--border);background:#fff;}
    .detail-card .label{font-size:11px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);}
    .detail-card .value{margin-top:10px;font-size:22px;font-weight:900;color:var(--text);}
    .detail-card .sub{margin-top:6px;font-size:12px;color:var(--gray);}

    /* ── Badges ── */
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

    /* ── Helper panel ── */
    .helper-panel{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;padding:14px 16px;border-radius:16px;background:#f7f8ff;border:1px solid var(--border);font-size:12px;color:var(--gray);}
    .helper-panel strong{color:var(--text);}

    /* ── Split head ── */
    .split-head{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:18px;}
    .split-head .section-title{margin-bottom:0;}

    /* ── History ── */
    .history-table td:last-child{white-space:nowrap;}
    .history-actions{display:flex;gap:8px;flex-wrap:wrap;}
    .btn-small{padding:9px 12px;border-radius:12px;font-size:12px;}
    .empty-mini{padding:26px 18px;border-radius:18px;border:1px dashed var(--border);text-align:center;color:var(--gray);}
    .client-empty-center{padding:64px 20px;text-align:center;color:var(--gray);}
    .client-empty-center i{font-size:48px;display:block;margin-bottom:16px;opacity:.3;}
    .client-empty-center strong{font-size:16px;display:block;margin-bottom:6px;color:var(--text);}

    /* ── Payment form grid ── */
    .pf-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;}
    .pf-grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:18px;}
    .pf-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;}
    @media(max-width:1000px){.pf-grid{grid-template-columns:repeat(2,1fr);}.pf-grid-3{grid-template-columns:repeat(2,1fr);}}
    @media(max-width:600px){.pf-grid,.pf-grid-2,.pf-grid-3{grid-template-columns:1fr;}}

    /* ── Payment form fields ── */
    .panel-body .form-group{display:flex;flex-direction:column;gap:6px;}
    .panel-body .form-group label{font-size:11px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);}
    .panel-body .form-group input,
    .panel-body .form-group select,
    .panel-body .form-group textarea{
        width:100%;border:1.5px solid #e2e8f0;background:#f8faff;border-radius:12px;
        padding:12px 16px;font:600 13px 'Poppins',sans-serif;color:var(--text);
        outline:none;transition:.2s;box-sizing:border-box;
    }
    .panel-body .form-group input:focus,
    .panel-body .form-group select:focus,
    .panel-body .form-group textarea:focus{border-color:#29b6d8;background:#fff;box-shadow:0 0 0 3px rgba(41,182,216,.1);}
    .panel-body .form-group input::placeholder,
    .panel-body .form-group textarea::placeholder{color:#a0aec0;font-weight:500;}
    .panel-body .form-group select{cursor:pointer;appearance:auto;}
    .panel-body .helper-text{font-size:11.5px;color:var(--gray);line-height:1.6;margin-top:4px;}
    .panel-body .helper-text strong{color:#1d4ed8;}
    .panel-body .error-text{font-size:11px;color:#dc2626;margin-top:2px;}

    /* ── Botón registrar pago full-width ── */
    .btn-registrar-pago{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;margin-top:24px;padding:17px;border:none;border-radius:14px;background:linear-gradient(135deg,#29b6d8,#1a8fbf);color:#fff;font:800 14px 'Poppins',sans-serif;letter-spacing:1px;cursor:pointer;transition:.2s;box-shadow:0 4px 16px rgba(41,182,216,.35);}
    .btn-registrar-pago:hover{background:linear-gradient(135deg,#1a8fbf,#1270a0);transform:translateY(-1px);box-shadow:0 6px 20px rgba(41,182,216,.45);}

    /* ── Panel Card (Datos / Pago / Historial) ── */
    .panel-card{border-radius:18px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.07);border:1.5px solid var(--border);}
    .panel-head{background:linear-gradient(135deg,#29b6d8 0%,#1a8fbf 100%);color:#fff;padding:16px 24px;font-size:15px;font-weight:800;display:flex;align-items:center;gap:10px;position:relative;}
    .panel-head i{font-size:16px;opacity:.9;}
    .panel-head-count{margin-left:auto;background:rgba(255,255,255,.2);border-radius:999px;padding:3px 12px;font-size:11px;font-weight:700;}
    .panel-back-btn{margin-left:auto;display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);color:#fff;font:700 12px 'Poppins',sans-serif;text-decoration:none;transition:.2s;}
    .panel-back-btn:hover{background:rgba(255,255,255,.28);}
    .panel-body{background:#fff;padding:22px 24px;}

    /* ── Client info grid ── */
    .client-info-grid{display:grid;gap:0;border:1.5px solid #e2e8f0;border-radius:12px;overflow:hidden;}
    .ci-row{display:grid;grid-template-columns:repeat(3,1fr);border-bottom:1px solid #e2e8f0;}
    .ci-row:last-child{border-bottom:none;}
    .ci-field{padding:12px 16px;border-right:1px solid #e2e8f0;display:flex;align-items:center;gap:8px;font-size:13px;}
    .ci-field:last-child{border-right:none;}
    .ci-label{color:#4f46e5;font-weight:700;white-space:nowrap;min-width:90px;}
    .ci-val{color:var(--text);font-weight:600;}

    /* ── Btn danger ── */
    .btn-danger{display:inline-flex;align-items:center;gap:6px;padding:9px 12px;border-radius:10px;background:#fee2e2;color:#b91c1c;border:1.5px solid #fecaca;font:700 12px 'Poppins',sans-serif;cursor:pointer;transition:.2s;}
    .btn-danger:hover{background:#fecaca;border-color:#f87171;}

    /* ── History table inside panel ── */
    .history-table thead th{background:#f8fafc;padding:12px 16px;text-align:left;border-bottom:1.5px solid var(--border);font-size:10.5px;font-weight:800;letter-spacing:.7px;text-transform:uppercase;color:var(--gray);}
    .history-table tbody td{padding:14px 16px;border-bottom:1px solid var(--border2);font-size:13px;color:var(--text);vertical-align:middle;}
    .history-table tbody tr:last-child td{border-bottom:none;}
    .history-table tbody tr:hover td{background:#fafaff;}
    .history-actions{display:flex;gap:8px;align-items:center;}

    /* ── Resumen tab ── */
    .resumen-filters{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;margin-bottom:22px;}
    .resumen-filters .form-group{margin-bottom:0;min-width:180px;}
    .toolbar-select{width:100%;border:1.5px solid var(--border);background:#fff;border-radius:14px;padding:12px 14px;font:600 13px 'Poppins',sans-serif;color:var(--text);}
    .client-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px;}
    .client-card{display:block;padding:18px;border-radius:18px;border:1.5px solid var(--border);background:#fff;text-decoration:none;transition:.2s;}
    .client-card:hover{border-color:rgba(85,51,204,.35);transform:translateY(-2px);box-shadow:0 8px 24px rgba(85,51,204,.08);}
    .client-card-title{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:10px;}
    .client-name{font-size:14px;font-weight:800;color:var(--text);}
    .client-meta{font-size:12px;color:var(--gray);line-height:1.6;}

    /* ── Summary cards row ── */
    .summary-grid-5{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin-bottom:24px;}

    /* ── Client found banner ── */
    .client-found-banner{display:flex;align-items:center;gap:16px;padding:16px 20px;background:linear-gradient(135deg,#f0eeff,#e8e4ff);border-radius:16px;border:1.5px solid rgba(85,51,204,.2);margin-bottom:22px;}
    .client-found-avatar{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#4f46e5,#6366f1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:900;flex-shrink:0;}
    .client-found-info{flex:1;}
    .client-found-name{font-size:16px;font-weight:900;color:var(--text);}
    .client-found-meta{font-size:12px;color:var(--gray);margin-top:2px;}

    /* ── Seleccionar Lote ── */
    .lot-select-card{border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(85,51,204,.10);margin-bottom:24px;}
    .lot-select-head{background:linear-gradient(135deg,#f59e0b 0%,#d97706 100%);padding:22px 28px;position:relative;overflow:hidden;}
    .lot-select-head::before{content:'';position:absolute;right:-40px;top:-40px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.1);}
    .lot-select-head h2{font-size:20px;font-weight:900;color:#fff;margin:0;position:relative;z-index:1;display:flex;align-items:center;gap:10px;}
    .lot-select-body{background:#fff;padding:24px 28px;}
    .lot-select-alert{display:flex;align-items:center;gap:12px;padding:13px 16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;font-size:13px;color:#1d4ed8;margin-bottom:20px;}
    .lot-select-alert i{font-size:16px;flex-shrink:0;}
    .lot-select-table{width:100%;border-collapse:collapse;}
    .lot-select-table thead th{padding:11px 16px;text-align:left;background:#f8fafc;border-bottom:1.5px solid var(--border);font-size:10.5px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);}
    .lot-select-table tbody td{padding:14px 16px;border-bottom:1px solid var(--border2);font-size:13px;color:var(--text);vertical-align:middle;}
    .lot-select-table tbody tr:last-child td{border-bottom:none;}
    .lot-select-table tbody tr:hover td{background:#fafaff;}
    .btn-select-lot{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:10px;background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;font:700 12px 'Poppins',sans-serif;text-decoration:none;letter-spacing:.3px;transition:.2s;border:none;cursor:pointer;}
    .btn-select-lot:hover{transform:translateY(-1px);box-shadow:0 6px 16px rgba(79,70,229,.3);}
    .lot-estado-badge{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:999px;font-size:11.5px;font-weight:700;}
    .lot-estado-badge::before{content:'';width:7px;height:7px;border-radius:50%;}
    .lot-estado-contado{background:#dcfce7;color:#15803d;}.lot-estado-contado::before{background:#16a34a;}
    .lot-estado-reservado{background:#fef3c7;color:#b45309;}.lot-estado-reservado::before{background:#d97706;}
    .lot-estado-financiamiento{background:#dbeafe;color:#1d4ed8;}.lot-estado-financiamiento::before{background:#2563eb;}
    .lot-estado-sin_pagos{background:#f1f5f9;color:#64748b;}.lot-estado-sin_pagos::before{background:#94a3b8;}

    @media(max-width:1000px){.summary-inline,.detail-grid{grid-template-columns:repeat(2,minmax(0,1fr));}.summary-grid-5{grid-template-columns:repeat(3,minmax(0,1fr));}.search-row{flex-direction:column;}.btn-search{width:100%;justify-content:center;}}
    @media(max-width:700px){.summary-inline,.detail-grid,.helper-panel,.summary-grid-5{grid-template-columns:1fr;}.client-grid{grid-template-columns:1fr;}.resumen-filters{flex-direction:column;}.resumen-filters .form-group{width:100%;}}
</style>
@endpush

@section('content')

{{-- ── Tabs ── --}}
@php
    $activeTab = request('tab', 'registro');
@endphp
<div class="cob-tabs">
    <a
        href="{{ route('admin.proyectos.cobranza', array_merge(['proyecto' => $proyecto->slug], request()->only(['dni','lote','manzana','modalidad','estado','search_mode']), ['tab' => 'registro'])) }}"
        class="cob-tab {{ $activeTab === 'registro' ? 'active' : '' }}"
    >
        <i class="fas fa-money-bill-wave"></i> Registro de Pagos
    </a>
    <a
        href="{{ route('admin.proyectos.cobranza', array_merge(['proyecto' => $proyecto->slug], request()->only(['dni','lote','manzana','modalidad','estado']), ['tab' => 'resumen'])) }}"
        class="cob-tab {{ $activeTab === 'resumen' ? 'active' : '' }}"
    >
        <i class="fas fa-users"></i> Resumen de Clientes
    </a>
</div>

{{-- ════════════════════════════════════════════════
     TAB 1 — REGISTRO DE PAGOS
═════════════════════════════════════════════════ --}}
@if($activeTab === 'registro')

{{-- Search Hero Card --}}
@php
    $searchMode = request('search_mode', 'dni');
@endphp
<div class="search-hero">
    <div class="search-hero-head">
        <h2><i class="fas fa-magnifying-glass-dollar" style="margin-right:10px;opacity:.85;"></i>Buscar Cliente</h2>
    </div>
    <div class="search-hero-body">

        {{-- Mode toggle --}}
        <div class="mode-toggle">
            <a
                href="{{ route('admin.proyectos.cobranza', array_merge(['proyecto' => $proyecto->slug], request()->only(['modalidad','estado']), ['tab' => 'registro', 'search_mode' => 'dni'])) }}"
                class="mode-btn {{ $searchMode === 'dni' ? 'is-active' : 'is-inactive' }}"
            >
                <i class="fas fa-id-card"></i> Por DNI
            </a>
            <a
                href="{{ route('admin.proyectos.cobranza', array_merge(['proyecto' => $proyecto->slug], request()->only(['modalidad','estado']), ['tab' => 'registro', 'search_mode' => 'lote'])) }}"
                class="mode-btn {{ $searchMode === 'lote' ? 'is-active' : 'is-inactive' }}"
            >
                <i class="fas fa-map-location-dot"></i> Por Manzana-Lote
            </a>
        </div>

        {{-- Search form --}}
        <form method="GET" action="{{ route('admin.proyectos.cobranza', $proyecto) }}">
            <input type="hidden" name="tab" value="registro">
            <input type="hidden" name="search_mode" value="{{ $searchMode }}">

            @if($searchMode === 'dni')
            <div class="search-row">
                <div class="search-fields">
                    <div class="search-field-wrap">
                        <label>DNI del Cliente</label>
                        <input
                            type="text"
                            name="dni"
                            value="{{ $dni }}"
                            class="search-input"
                            placeholder="Ej: 12345678"
                            maxlength="8"
                        >
                    </div>
                </div>
                <button type="submit" class="btn-search">
                    <i class="fas fa-magnifying-glass"></i> BUSCAR
                </button>
            </div>
            @else
            <div class="search-row">
                <div class="search-fields two-col">
                    <div class="search-field-wrap">
                        <label>Manzana</label>
                        <input
                            type="text"
                            name="manzana"
                            value="{{ $manzana }}"
                            class="search-input"
                            placeholder="Ej: A"
                        >
                    </div>
                    <div class="search-field-wrap">
                        <label>Numero de Lote</label>
                        <input
                            type="text"
                            name="lote"
                            value="{{ $lote }}"
                            class="search-input"
                            placeholder="Ej: 12"
                        >
                    </div>
                </div>
                <button type="submit" class="btn-search">
                    <i class="fas fa-magnifying-glass"></i> BUSCAR
                </button>
            </div>
            @endif
        </form>

    </div>
</div>

{{-- ── Client detail (when selected) ── --}}
@if($selectedClient)

{{-- ══ DATOS DEL CLIENTE ══ --}}
<div class="panel-card" style="margin-bottom:18px;">
    <div class="panel-head">
        <i class="fas fa-user-circle"></i> Datos del Cliente
        <a href="{{ route('admin.proyectos.cobranza', ['proyecto' => $proyecto->slug, 'tab' => 'registro', 'dni' => $dni, 'lote' => $lote, 'manzana' => $manzana, 'search_mode' => request('search_mode','dni')]) }}"
           class="panel-back-btn">
            <i class="fas fa-arrow-left"></i> Cambiar lote
        </a>
    </div>
    <div class="panel-body">
        <div class="client-info-grid">
            <div class="ci-row">
                <div class="ci-field"><span class="ci-label">Nombres:</span> <span class="ci-val">{{ $selectedClient->nombres ?? $selectedClient->nombre }}</span></div>
                <div class="ci-field"><span class="ci-label">Manzana:</span> <span class="ci-val">{{ $selectedClient->lote->manzana ?? '—' }}</span></div>
                <div class="ci-field"><span class="ci-label">Cuota Mensual:</span> <span class="ci-val">S/. {{ number_format((float)$selectedClient->cuota_mensual, 2, '.', ',') }}</span></div>
            </div>
            <div class="ci-row">
                <div class="ci-field"><span class="ci-label">Apellidos:</span> <span class="ci-val">{{ $selectedClient->apellidos }}</span></div>
                <div class="ci-field"><span class="ci-label">Lote:</span> <span class="ci-val">{{ $selectedClient->lote->numero ?? '—' }}</span></div>
                <div class="ci-field"><span class="ci-label">Estado:</span> <span class="ci-val">{{ ucfirst($selectedClient->modalidad) }}</span></div>
            </div>
            <div class="ci-row">
                <div class="ci-field"><span class="ci-label">DNI:</span> <span class="ci-val">{{ $selectedClient->dni }}</span></div>
                <div class="ci-field"><span class="ci-label">Precio del Lote:</span> <span class="ci-val">S/. {{ number_format((float)$selectedClient->precio_lote, 2, '.', ',') }}</span></div>
                <div class="ci-field">
                    <span class="ci-label">Saldo Pendiente:</span>
                    <span class="ci-val" style="color:#e53e3e;font-weight:800;">S/. {{ number_format((float)$selectedClient->saldo_pendiente, 2, '.', ',') }}</span>
                </div>
            </div>
        </div>
        <div style="display:flex;gap:10px;margin-top:14px;flex-wrap:wrap;">
            <a href="{{ route('admin.proyectos.cobranza.cronograma', [$proyecto, $selectedClient]) }}" class="btn-secondary btn-small">
                <i class="fas fa-calendar-days"></i> Ver cronograma
            </a>
            @if($selectedClient->cronogramaPagos()->exists())
            <form method="POST" action="{{ route('admin.proyectos.cobranza.cronograma.regenerar', [$proyecto, $selectedClient]) }}">
                @csrf
                <button type="submit" class="btn-secondary btn-small">
                    <i class="fas fa-rotate"></i> Regenerar cronograma
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

{{-- ══ REGISTRAR PAGO ══ --}}
<div class="panel-card" style="margin-bottom:18px;">
    <div class="panel-head">
        <i class="fas fa-{{ $editPayment ? 'pen' : 'plus-circle' }}"></i>
        {{ $editPayment ? 'Editar Pago' : 'Registrar Pago' }}
    </div>
    <div class="panel-body" style="{{ $editPayment ? '' : 'padding-bottom:0;' }}">
        @include('admin.proyectos.cobranza._payment-form', [
            'payment' => $editPayment ?: new \App\Models\Pago(),
        ])
    </div>
</div>

{{-- ══ HISTORIAL DE PAGOS ══ --}}
<div class="panel-card">
    <div class="panel-head">
        <i class="fas fa-history"></i> Historial de Pagos
        <span class="panel-head-count">{{ $historialPagos?->total() ?? 0 }} registros</span>
    </div>
    <div class="panel-body" style="padding:0;">
        <table class="history-table" style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Fecha de Pago</th>
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
                    <td class="muted">{{ $pago->notas ?: '—' }}</td>
                    <td>
                        <div class="history-actions">
                            @if($pago->estado_pago === 'registrado')
                            <a href="{{ route('admin.proyectos.cobranza', array_filter(['proyecto' => $proyecto->slug, 'tab' => 'registro', 'cliente' => $selectedClient->id, 'editar_pago' => $pago->id, 'dni' => $dni, 'search_mode' => request('search_mode','dni')], fn($v) => $v !== null && $v !== '')) }}"
                               class="btn-secondary btn-small">
                                <i class="fas fa-pen"></i> Editar
                            </a>
                            <form method="POST" action="{{ route('admin.proyectos.cobranza.pagos.destroy', [$proyecto, $pago]) }}"
                                  onsubmit="return confirm('Se anulará este pago y se recalcularán los saldos del cliente.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger btn-small">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @else
                            <span class="muted">—</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-receipt"></i>
                            <strong>Este cliente aún no tiene pagos registrados.</strong>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($historialPagos && $historialPagos->hasPages())
        <div class="pagination" style="padding:16px 22px;">
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
    </div>
</div>

@elseif($hasSearch && $clientes->total() > 0)
{{-- Resultados encontrados — pantalla de selección de lote --}}
<div class="lot-select-card">
    <div class="lot-select-head">
        <h2><i class="fas fa-layer-group"></i> Seleccionar Lote</h2>
    </div>
    <div class="lot-select-body">
        <div class="lot-select-alert">
            <i class="fas fa-circle-info"></i>
            @if($clientes->total() > 1)
                Este cliente tiene múltiples lotes. Por favor, seleccione el lote que desea administrar:
            @else
                Se encontró el siguiente lote. Haz clic en <strong>Seleccionar</strong> para administrarlo:
            @endif
        </div>

        <table class="lot-select-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Manzana</th>
                    <th>Lote</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Total Pagado</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientes as $clienteLote)
                <tr>
                    <td>
                        <div style="font-weight:700;font-size:13px;">{{ $clienteLote->nombre_completo }}</div>
                        <div style="font-size:11px;color:var(--gray);">DNI {{ $clienteLote->dni }}</div>
                    </td>
                    <td class="cell-strong">{{ $clienteLote->lote->manzana ?? '—' }}</td>
                    <td class="cell-strong">{{ $clienteLote->lote->numero ?? '—' }}</td>
                    <td>S/. {{ number_format((float) $clienteLote->precio_lote, 2, '.', ',') }}</td>
                    <td>
                        <span class="lot-estado-badge lot-estado-{{ $clienteLote->modalidad }}">
                            {{ ucfirst($clienteLote->modalidad) }}
                        </span>
                    </td>
                    <td>S/. {{ number_format((float) $clienteLote->total_pagado, 2, '.', ',') }}</td>
                    <td>
                        <a
                            href="{{ route('admin.proyectos.cobranza', array_filter(['proyecto' => $proyecto->slug, 'tab' => 'registro', 'cliente' => $clienteLote->id, 'dni' => $dni, 'lote' => $lote, 'manzana' => $manzana, 'search_mode' => request('search_mode','dni')], fn($v) => $v !== null && $v !== '')) }}"
                            class="btn-select-lot"
                        >
                            <i class="fas fa-circle-check"></i> SELECCIONAR
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@elseif($hasSearch && $clientes->total() === 0)
{{-- Sin resultados --}}
<div class="card content-card">
    <div class="client-empty-center">
        <i class="fas fa-magnifying-glass"></i>
        <strong>Sin resultados</strong>
        <span>No se encontró ningún cliente con esos datos en este proyecto.</span>
    </div>
</div>

@else
{{-- Estado inicial --}}
<div class="card content-card">
    <div class="client-empty-center">
        <i class="fas fa-user-magnifying-glass"></i>
        <strong>Ningún cliente seleccionado</strong>
        <span>Usa el buscador de arriba para encontrar un cliente y registrar su cobranza.</span>
    </div>
</div>
@endif

{{-- ════════════════════════════════════════════════
     TAB 2 — RESUMEN DE CLIENTES
═════════════════════════════════════════════════ --}}
@else

{{-- Summary cards --}}
@php
    $resCards = [
        ['key' => 'Total',          'class' => 'is-total',         'icon' => 'fas fa-wallet',      'label' => 'Cartera total'],
        ['key' => 'reservado',      'class' => 'is-reservado',     'icon' => 'fas fa-clock',       'label' => 'Reservados'],
        ['key' => 'financiamiento', 'class' => 'is-financiamiento','icon' => 'fas fa-credit-card', 'label' => 'Financiamiento'],
        ['key' => 'pagado',         'class' => 'is-libre',         'icon' => 'fas fa-circle-check','label' => 'Pagados'],
        ['key' => 'desistido',      'class' => 'is-vendido',       'icon' => 'fas fa-user-xmark',  'label' => 'Desistidos'],
    ];
@endphp
<section class="summary-grid-5">
    @foreach($resCards as $card)
    <article class="card summary-card {{ $card['class'] }}">
        <div class="summary-icon"><i class="{{ $card['icon'] }}"></i></div>
        <div>
            <h3>{{ $resumen[$card['key']] ?? 0 }}</h3>
            <p>{{ $card['label'] }}</p>
        </div>
    </article>
    @endforeach
</section>

{{-- Filters --}}
<section class="card content-card" style="margin-bottom:22px;">
    <form method="GET" action="{{ route('admin.proyectos.cobranza', $proyecto) }}" class="resumen-filters">
        <input type="hidden" name="tab" value="resumen">

        <div class="form-group">
            <label class="mini-label">Buscar por DNI</label>
            <div class="search-box" style="margin-top:8px;">
                <i class="fas fa-id-card"></i>
                <input type="text" name="dni" value="{{ $dni }}" placeholder="DNI del cliente">
            </div>
        </div>

        <div class="form-group">
            <label class="mini-label">Buscar por lote</label>
            <div class="search-box" style="margin-top:8px;">
                <i class="fas fa-map-location-dot"></i>
                <input type="text" name="lote" value="{{ $lote }}" placeholder="Manzana, lote o codigo">
            </div>
        </div>

        <div class="form-group">
            <label class="mini-label">Modalidad</label>
            <select name="modalidad" class="toolbar-select" style="margin-top:8px;">
                <option value="">Todas</option>
                @foreach($modalidades as $item)
                <option value="{{ $item }}" @selected($modalidad === $item)>{{ ucfirst($item) }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="mini-label">Estado financiero</label>
            <select name="estado" class="toolbar-select" style="margin-top:8px;">
                <option value="">Todos</option>
                @foreach($estadosCobranza as $item)
                <option value="{{ $item }}" @selected($estado === $item)>{{ str_replace('_', ' ', ucfirst($item)) }}</option>
                @endforeach
            </select>
        </div>

        <div style="display:flex;gap:8px;align-items:flex-end;">
            <button type="submit" class="btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
            <a href="{{ route('admin.proyectos.cobranza', ['proyecto' => $proyecto, 'tab' => 'resumen']) }}" class="btn-secondary">Limpiar</a>
        </div>
    </form>
</section>

{{-- Client grid --}}
<section class="card content-card">
    <div class="split-head">
        <div class="section-title">Clientes en <span>Cobranza</span></div>
        <span class="muted">{{ $clientes->total() }} resultados</span>
    </div>

    <div class="client-grid">
        @forelse($clientes as $cliente)
        <a
            href="{{ route('admin.proyectos.cobranza', array_filter(array_merge(['proyecto' => $proyecto], request()->except(['cliente','editar_pago','clientes_page','tab']), ['tab' => 'registro', 'cliente' => $cliente->id, 'search_mode' => 'dni', 'dni' => $cliente->dni]), fn ($value) => $value !== null && $value !== '')) }}"
            class="client-card"
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
        <div style="grid-column:1/-1;">
            <div class="client-empty-center">
                <i class="fas fa-search-dollar"></i>
                <strong>No hay clientes con los filtros actuales.</strong>
                <span>Prueba cambiando los criterios de busqueda.</span>
            </div>
        </div>
        @endforelse
    </div>

    @if($clientes->hasPages())
    <div class="pagination" style="margin-top:20px;">
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

@endif

@endsection
