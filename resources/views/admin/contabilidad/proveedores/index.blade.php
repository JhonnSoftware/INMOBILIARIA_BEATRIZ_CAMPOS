@extends('layouts.admin-main', ['currentModule' => 'proveedores'])

@section('title', 'Proveedores | BC Inmobiliaria')
@section('topbar_title')Gestion de <span>Proveedores</span>@endsection
@section('module_label', 'Proveedores')
@section('page_title', 'Proveedores Corporativos')
@section('page_subtitle', 'Controla el maestro de proveedores, categorias, medios de pago, saldos pendientes y contratos en un solo lugar.')

@php
    $isEditing = $editingProveedor !== null;
    $current = $editingProveedor ?? new \App\Models\Proveedor(['monto_total' => 0, 'monto_pagado' => 0]);

    $selectedCategoria = old('categoria_select');
    if ($selectedCategoria === null) {
        $selectedCategoria = in_array((string) $current->categoria, $categorias, true) ? $current->categoria : '';
    }

    $categoriaNueva = old('categoria_nueva');
    if ($categoriaNueva === null) {
        $categoriaNueva = $current->categoria && ! in_array((string) $current->categoria, $categorias, true) ? $current->categoria : '';
    }

    $subcategoriasActuales = $selectedCategoria !== '' ? ($subcategoriasPorCategoria[$selectedCategoria] ?? []) : [];

    $selectedSubcategoria = old('subcategoria_select');
    if ($selectedSubcategoria === null) {
        $selectedSubcategoria = in_array((string) $current->subcategoria, $subcategoriasActuales, true) ? $current->subcategoria : '';
    }

    $subcategoriaNueva = old('subcategoria_nueva');
    if ($subcategoriaNueva === null) {
        $subcategoriaNueva = $current->subcategoria && ! in_array((string) $current->subcategoria, $subcategoriasActuales, true) ? $current->subcategoria : '';
    }

    $pendingPreview = max((float) old('monto_total', $current->monto_total ?: 0) - (float) old('monto_pagado', $current->monto_pagado ?: 0), 0);

    $metricCards = [
        ['icon' => 'fas fa-triangle-exclamation', 'accent' => '#ef4444', 'soft' => '#fee2e2', 'value' => $summary['pagos_vencidos'], 'label' => 'Pagos Vencidos', 'helper' => 'Con saldo pendiente', 'currency' => false],
        ['icon' => 'fas fa-bell', 'accent' => '#f59e0b', 'soft' => '#fef3c7', 'value' => $summary['proximos_vencer'], 'label' => 'Proximos a Vencer', 'helper' => 'En 7 dias', 'currency' => false],
        ['icon' => 'fas fa-sack-dollar', 'accent' => '#16a34a', 'soft' => '#dcfce7', 'value' => $summary['monto_total_general'], 'label' => 'Monto Total General', 'helper' => 'Base registrada', 'currency' => true],
        ['icon' => 'fas fa-credit-card', 'accent' => '#2f8ce5', 'soft' => '#dbeafe', 'value' => $summary['total_pagado'], 'label' => 'Total Pagado', 'helper' => 'Pagos cubiertos', 'currency' => true],
        ['icon' => 'fas fa-chart-column', 'accent' => '#ef4444', 'soft' => '#fee2e2', 'value' => $summary['total_pendiente'], 'label' => 'Total Pendiente', 'helper' => 'Saldo por cubrir', 'currency' => true],
        ['icon' => 'fas fa-calculator', 'accent' => '#e91e63', 'soft' => '#fce7f3', 'value' => $summary['con_yape_plin'], 'label' => 'Con Yape/Plin', 'helper' => 'Pago digital', 'currency' => false],
        ['icon' => 'fas fa-building-columns', 'accent' => '#9b59d0', 'soft' => '#f3e8ff', 'value' => $summary['con_cuenta_bancaria'], 'label' => 'Con Cuenta Bancaria', 'helper' => 'Transferencias', 'currency' => false],
    ];
@endphp

@push('styles')
<style>
    .providers-page{display:grid;gap:24px;min-width:0;width:100%}.providers-hero{padding:22px;background:#162f38;border-radius:26px;display:grid;gap:22px;box-shadow:0 18px 44px rgba(15,23,42,.12);min-width:0;width:100%;box-sizing:border-box}.metric-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.metric-card{background:#fff;border-radius:18px;padding:16px 14px;display:grid;grid-template-columns:auto 1fr;gap:12px;align-items:center;box-shadow:0 14px 32px rgba(15,23,42,.12);border-left:4px solid var(--metric-accent);min-width:0}.metric-icon{width:46px;height:46px;flex-shrink:0;border-radius:50%;background:var(--metric-soft);display:flex;align-items:center;justify-content:center;color:var(--metric-accent);font-size:20px}.metric-value{font-size:16px;font-weight:900;color:var(--metric-accent);line-height:1.1;word-break:break-all}.metric-value.currency{font-size:15px}.metric-label{margin-top:4px;font-size:12px;font-weight:700;color:#475569;line-height:1.3}.metric-helper{margin-top:2px;font-size:10.5px;color:#94a3b8}.category-board{background:#fff;border-radius:22px;padding:24px 26px 28px;box-shadow:0 16px 38px rgba(15,23,42,.12);min-width:0;box-sizing:border-box}.category-board-head,.module-head{display:flex;align-items:center;gap:10px;padding-bottom:12px;border-bottom:2px solid #4b82ff;font-size:18px;font-weight:900;color:#204a8b}.category-board-head i{color:#7dd6cd}.category-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-top:18px}.category-card{padding:18px;border-radius:18px;background:#fff;border-left:4px solid var(--category-border);box-shadow:0 14px 30px rgba(15,23,42,.08);display:grid;grid-template-columns:auto 1fr;gap:14px;align-items:center}.category-icon{width:62px;height:62px;border-radius:50%;background:var(--category-soft);display:flex;align-items:center;justify-content:center;color:var(--category-accent);font-size:30px}.category-meta strong{display:block;font-size:18px;font-weight:900;color:var(--category-text)}.category-meta span{display:block;margin-top:2px;font-size:16px;font-weight:800;color:var(--category-text)}.category-meta small{display:block;margin-top:4px;font-size:13px;color:#64748b;line-height:1.6}.module-card{padding:24px 24px 26px;border-radius:24px;background:#fff;border:1.5px solid var(--border);box-shadow:0 10px 28px rgba(15,23,42,.05);min-width:0;width:100%;box-sizing:border-box}.module-head i{color:#8b5cf6;font-size:18px}.provider-form{display:grid;gap:18px;margin-top:18px}.provider-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px 18px;width:100%;box-sizing:border-box}.provider-field{display:grid;gap:8px;min-width:0}.provider-field.span-6{grid-column:span 1}.provider-field.span-4{grid-column:span 1}.provider-field.span-12{grid-column:1 / -1}.provider-field label{font-size:12px;font-weight:800;color:var(--text)}.provider-field input,.provider-field select{width:100%;border:1.5px solid var(--border);background:#fff;border-radius:12px;padding:12px 14px;outline:none;font:500 13px 'Poppins',sans-serif;color:var(--text);transition:.18s}.provider-field input:focus,.provider-field select:focus{border-color:rgba(59,130,246,.32);box-shadow:0 0 0 4px rgba(59,130,246,.08)}.provider-field small{font-size:11px;color:#94a3b8;line-height:1.5}.provider-error{font-size:11.5px;color:#dc2626;font-weight:600}.line-separator{grid-column:1 / -1;height:1px;background:#e5e7eb;margin:2px 0}.field-icon{display:inline-flex;align-items:center;gap:6px}.field-icon i{font-size:12px;color:#8b5cf6}.select-with-action{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:10px;align-items:center}.toggle-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 14px;border:none;border-radius:10px;background:#22c55e;color:#fff;font:700 12px 'Poppins',sans-serif;cursor:pointer;white-space:nowrap}.toggle-btn:hover{opacity:.92}.custom-input{display:none}.custom-input.active{display:block}.pending-box{height:100%;min-height:110px;border:1.5px dashed #60a5fa;border-radius:16px;background:#f8fbff;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:16px}.pending-box span{font-size:14px;font-weight:700;color:#475569}.pending-box strong{display:block;margin-top:8px;font-size:28px;font-weight:900;color:#16a34a}.pending-box small{display:block;margin-top:6px;font-size:11px;color:#94a3b8}.info-strip{grid-column:1 / -1;padding:12px 14px;border-radius:12px;background:#f8fafc;color:#475569;font-size:12px;line-height:1.6;border:1px solid #eef2f7}.info-strip i{color:#f59e0b;margin-right:6px}.upload-box{grid-column:1 / -1;border:1.5px dashed #60a5fa;border-radius:14px;background:#fbfdff;padding:14px 16px}.upload-box input{padding:0;border:none;background:transparent;box-shadow:none}.upload-note{margin-top:10px;padding:10px 12px;border-radius:10px;background:#fff6db;color:#a16207;font-size:11.5px;line-height:1.6}.existing-link{margin-top:10px;font-size:12px;color:#64748b}.existing-link a{color:#2563eb;font-weight:700;text-decoration:none}.existing-link a:hover{text-decoration:underline}.provider-actions{display:flex;gap:10px;flex-wrap:wrap;align-items:center}.provider-actions .btn-primary{background:linear-gradient(135deg,#1d4ed8,#274f92);box-shadow:0 10px 26px rgba(29,78,216,.22)}.provider-actions .btn-primary:hover{box-shadow:0 14px 32px rgba(29,78,216,.28)}.providers-table-shell{margin-top:18px}.providers-table-wrap{overflow-x:auto;border-radius:18px;border:1px solid #dbe4ff;background:#fff}.providers-table{width:100%;border-collapse:separate;border-spacing:0}.providers-table thead th{background:#274f92;padding:16px 14px;color:#fff;font-size:12px;font-weight:800;text-align:left;white-space:nowrap}.providers-table tbody td{padding:18px 14px;font-size:12.5px;color:var(--text);border-bottom:1px solid #edf2fb;vertical-align:middle}.providers-table tbody tr:last-child td{border-bottom:none}.providers-table tbody tr:hover td{background:#fbfdff}.company-cell strong{display:block;font-size:14px;font-weight:800;color:var(--text)}.company-cell span{display:block;margin-top:4px;font-size:11px;color:#94a3b8}.provider-badge{display:inline-flex;align-items:center;gap:7px;padding:7px 12px;border-radius:10px;font-size:11.5px;font-weight:700}.location-text{line-height:1.65;color:#334155}.digital-pill{display:inline-flex;align-items:center;justify-content:center;padding:8px 12px;border-radius:999px;background:#6f33a7;color:#fff;font-size:11.5px;font-weight:800;text-align:center;min-width:84px}.money-total{font-weight:900;color:#2f8ce5;white-space:nowrap}.money-paid{font-weight:900;color:#16a34a;white-space:nowrap}.money-pending{font-weight:900;color:#ef4444;white-space:nowrap}.supplier-cards{display:none;gap:16px;margin-top:18px}.supplier-card{padding:18px;border-radius:20px;background:#fff;border:1px solid #dbe4ff;box-shadow:0 10px 28px rgba(15,23,42,.06);display:grid;gap:14px}.supplier-card-head{display:flex;align-items:flex-start;justify-content:space-between;gap:14px}.supplier-card-head strong{display:block;font-size:16px;font-weight:900;color:var(--text)}.supplier-card-head span{display:block;margin-top:4px;font-size:12px;color:#64748b}.supplier-card-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px 12px}.supplier-item{padding:12px 13px;border-radius:14px;background:#f8fafc;border:1px solid #edf2fb}.supplier-item .label{display:block;font-size:10.5px;font-weight:800;letter-spacing:.6px;text-transform:uppercase;color:#94a3b8}.supplier-item .value{display:block;margin-top:6px;font-size:12.5px;line-height:1.65;color:var(--text);overflow-wrap:anywhere}.supplier-actions{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end}.action-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:9px 12px;border-radius:10px;border:none;text-decoration:none;color:#fff;font:700 12px 'Poppins',sans-serif;cursor:pointer}.action-btn.edit{background:#16a34a}.action-btn.delete{background:#ef4444}.action-btn.view{background:#2563eb}.action-btn:hover{opacity:.92}.empty-state{padding:42px 20px;text-align:center;color:#64748b}.empty-state i{font-size:38px;display:block;margin-bottom:12px;opacity:.42}.pagination-row{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:18px}.pagination-row .muted{font-size:12px;color:#64748b}@media(max-width:1500px){.providers-table-shell{display:none}.supplier-cards{display:grid;grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:1300px){.metric-grid{grid-template-columns:repeat(4,minmax(0,1fr))}.category-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:1100px){.metric-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:860px){.metric-grid,.category-grid,.supplier-cards,.supplier-card-grid,.provider-grid,.provider-field.span-6,.provider-field.span-4{grid-template-columns:1fr;grid-column:1}.provider-actions{flex-direction:column;align-items:stretch}.provider-actions .btn-primary,.provider-actions .btn-secondary{justify-content:center}.supplier-card-head{display:grid;grid-template-columns:1fr}.supplier-actions{justify-content:flex-start}.select-with-action{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<section class="providers-page">
    <section class="providers-hero">
        <div class="metric-grid">
            @foreach($metricCards as $card)
            <article class="metric-card" style="--metric-accent:{{ $card['accent'] }};--metric-soft:{{ $card['soft'] }};">
                <div class="metric-icon"><i class="{{ $card['icon'] }}"></i></div>
                <div>
                    <div class="metric-value {{ $card['currency'] ? 'currency' : '' }}">
                        @if($card['currency']) S/ {{ number_format((float) $card['value'], 2, '.', ',') }} @else {{ $card['value'] }} @endif
                    </div>
                    <div class="metric-label">{{ $card['label'] }}</div>
                    <div class="metric-helper">{{ $card['helper'] }}</div>
                </div>
            </article>
            @endforeach
        </div>

        <article class="category-board">
            <div class="category-board-head"><i class="fas fa-chart-simple"></i><span>Resumen por Categorias</span></div>
            <div class="category-grid">
                @foreach($categoryCards as $card)
                <article class="category-card" style="--category-border:{{ $card['style']['border'] }};--category-soft:{{ $card['style']['soft'] }};--category-accent:{{ $card['style']['accent'] }};--category-text:{{ $card['style']['text'] }};">
                    <div class="category-icon"><i class="{{ $card['style']['icon'] }}"></i></div>
                    <div class="category-meta">
                        <strong>{{ $card['total_proveedores'] }}</strong>
                        <span>{{ $card['total_proveedores'] === 1 ? 'Proveedor' : 'Proveedores' }}</span>
                        <small>{{ $card['categoria'] }}<br>Pendiente: S/ {{ number_format((float) $card['total_pendiente'], 2, '.', ',') }}</small>
                    </div>
                </article>
                @endforeach
            </div>
        </article>
    </section>

    <article class="module-card">
        <div class="module-head"><i class="fas fa-plus"></i><span>{{ $isEditing ? 'Editar Proveedor' : 'Agregar Nuevo Proveedor' }}</span></div>

        @if($errors->any())
        <div style="margin-top:16px;padding:14px 16px;border-radius:14px;background:#fff1f2;border:1px solid #fecdd3;color:#b91c1c;font-size:13px;line-height:1.7;">
            <strong>Corrige los datos del formulario:</strong>
            <ul style="margin:8px 0 0 18px;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ $isEditing ? route('admin.contabilidad.proveedores.update', $current) : route('admin.contabilidad.proveedores.store') }}" enctype="multipart/form-data" class="provider-form">
            @csrf
            @if($isEditing) @method('PUT') @endif

            <div class="provider-grid">
                <div class="provider-field span-6">
                    <label for="empresa">Nombre/Empresa *</label>
                    <input type="text" id="empresa" name="empresa" value="{{ old('empresa', $current->empresa) }}" required maxlength="191">
                    @error('empresa') <div class="provider-error">{{ $message }}</div> @enderror
                </div>
                <div class="provider-field span-6">
                    <label for="ruc">RUC *</label>
                    <input type="text" id="ruc" name="ruc" value="{{ old('ruc', $current->ruc) }}" required maxlength="20">
                    @error('ruc') <div class="provider-error">{{ $message }}</div> @enderror
                </div>
                <div class="provider-field span-6">
                    <label for="persona_contacto">Persona de Contacto</label>
                    <input type="text" id="persona_contacto" name="persona_contacto" value="{{ old('persona_contacto', $current->persona_contacto) }}" maxlength="150">
                    @error('persona_contacto') <div class="provider-error">{{ $message }}</div> @enderror
                </div>
                <div class="provider-field span-6">
                    <label for="telefono">Telefono</label>
                    <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $current->telefono) }}" maxlength="40">
                    @error('telefono') <div class="provider-error">{{ $message }}</div> @enderror
                </div>

                <div class="line-separator"></div>

                <div class="provider-field span-4">
                    <label for="departamento" class="field-icon"><i class="fas fa-globe-americas"></i> Departamento</label>
                    <input type="text" id="departamento" name="departamento" value="{{ old('departamento', $current->departamento) }}" placeholder="Ej: Junin, Lima, Cusco" maxlength="120">
                    @error('departamento') <div class="provider-error">{{ $message }}</div> @enderror
                </div>
                <div class="provider-field span-4">
                    <label for="provincia" class="field-icon"><i class="fas fa-map-signs"></i> Provincia</label>
                    <input type="text" id="provincia" name="provincia" value="{{ old('provincia', $current->provincia) }}" placeholder="Ej: Huancayo, Lima, Cusco" maxlength="120">
                    @error('provincia') <div class="provider-error">{{ $message }}</div> @enderror
                </div>
                <div class="provider-field span-4">
                    <label for="distrito" class="field-icon"><i class="fas fa-location-dot"></i> Distrito</label>
                    <input type="text" id="distrito" name="distrito" value="{{ old('distrito', $current->distrito) }}" placeholder="Ej: El Tambo, Miraflores, Wanchaq" maxlength="120">
                    @error('distrito') <div class="provider-error">{{ $message }}</div> @enderror
                </div>

                <div class="provider-field span-6">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $current->email) }}" maxlength="191">
                    @error('email') <div class="provider-error">{{ $message }}</div> @enderror
                </div>
                <div class="provider-field span-6">
                    <label for="categoria_select">Categoria *</label>
                    <div class="select-with-action">
                        <select id="categoria_select" name="categoria_select" data-selected="{{ $selectedCategoria }}" @disabled($categoriaNueva !== '')>
                            <option value="">-- Seleccione categoria --</option>
                            @foreach($categorias as $categoria)
                            <option value="{{ $categoria }}" @selected($selectedCategoria === $categoria)>{{ $categoria }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="toggle-btn" id="toggleCategoriaBtn"><i class="fas fa-plus"></i> {{ $categoriaNueva !== '' ? 'Usar lista' : 'Nueva' }}</button>
                    </div>
                    <div class="custom-input {{ $categoriaNueva !== '' ? 'active' : '' }}" id="categoriaNuevaWrap">
                        <input type="text" id="categoria_nueva" name="categoria_nueva" value="{{ $categoriaNueva }}" placeholder="Nueva categoria" maxlength="80">
                    </div>
                    @error('categoria') <div class="provider-error">{{ $message }}</div> @enderror
                </div>
                <div class="provider-field span-6">
                    <label for="subcategoria_select">Subcategoria *</label>
                    <div class="select-with-action">
                        <select id="subcategoria_select" name="subcategoria_select" data-selected="{{ $selectedSubcategoria }}" @disabled($subcategoriaNueva !== '')></select>
                        <button type="button" class="toggle-btn" id="toggleSubcategoriaBtn"><i class="fas fa-plus"></i> {{ $subcategoriaNueva !== '' ? 'Usar lista' : 'Nueva' }}</button>
                    </div>
                    <div class="custom-input {{ $subcategoriaNueva !== '' ? 'active' : '' }}" id="subcategoriaNuevaWrap">
                        <input type="text" id="subcategoria_nueva" name="subcategoria_nueva" value="{{ $subcategoriaNueva }}" placeholder="Nueva subcategoria" maxlength="80">
                    </div>
                    @error('subcategoria') <div class="provider-error">{{ $message }}</div> @enderror
                </div>
                <div class="provider-field span-6">
                    <label for="descripcion_servicio">Descripcion del Servicio</label>
                    <input type="text" id="descripcion_servicio" name="descripcion_servicio" value="{{ old('descripcion_servicio', $current->descripcion_servicio) }}" placeholder="Ej: Proveedor de redes sociales, diseno grafico" maxlength="191">
                    @error('descripcion_servicio') <div class="provider-error">{{ $message }}</div> @enderror
                </div>

                <div class="line-separator"></div>

                <div class="provider-field span-6">
                    <label for="yape_plin" class="field-icon"><i class="fas fa-calculator"></i> Numero YAPE/PLIN</label>
                    <input type="text" id="yape_plin" name="yape_plin" value="{{ old('yape_plin', $current->yape_plin) }}" placeholder="Ej: 987654321" maxlength="80">
                    <small>Para pagos digitales por Yape o Plin</small>
                    @error('yape_plin') <div class="provider-error">{{ $message }}</div> @enderror
                </div>
                <div class="provider-field span-6">
                    <label for="cuenta_bancaria" class="field-icon"><i class="fas fa-building-columns"></i> Numero de Cuenta Bancaria</label>
                    <input type="text" id="cuenta_bancaria" name="cuenta_bancaria" value="{{ old('cuenta_bancaria', $current->cuenta_bancaria) }}" placeholder="Ej: 194-1234567890-12 o CCI" maxlength="120">
                    <small>Numero de cuenta o CCI para transferencias bancarias</small>
                    @error('cuenta_bancaria') <div class="provider-error">{{ $message }}</div> @enderror
                </div>
                <div class="provider-field span-12">
                    <label for="proximo_pago">Fecha Proximo Pago</label>
                    <input type="date" id="proximo_pago" name="proximo_pago" value="{{ old('proximo_pago', optional($current->proximo_pago)->format('Y-m-d')) }}">
                    @error('proximo_pago') <div class="provider-error">{{ $message }}</div> @enderror
                </div>

                <div class="info-strip">
                    <i class="fas fa-lightbulb"></i>
                    <strong>Control de pagos:</strong> Ingresa el monto total del servicio o deuda y cuanto ya has pagado. El sistema calculara automaticamente lo que queda pendiente.
                </div>

                <div class="provider-field span-4">
                    <label for="monto_total">Monto Total (S/) *</label>
                    <input type="number" id="monto_total" name="monto_total" value="{{ old('monto_total', $current->monto_total ?: 0) }}" min="0" step="0.01" required>
                    @error('monto_total') <div class="provider-error">{{ $message }}</div> @enderror
                </div>
                <div class="provider-field span-4">
                    <label for="monto_pagado">Monto Pagado (S/)</label>
                    <input type="number" id="monto_pagado" name="monto_pagado" value="{{ old('monto_pagado', $current->monto_pagado ?: 0) }}" min="0" step="0.01">
                    @error('monto_pagado') <div class="provider-error">{{ $message }}</div> @enderror
                </div>
                <div class="provider-field span-4">
                    <div class="pending-box">
                        <span>Monto Pendiente</span>
                        <strong id="pendingPreview">S/ {{ number_format((float) $pendingPreview, 2, '.', ',') }}</strong>
                        <small>Calculado automaticamente</small>
                    </div>
                </div>

                <div class="provider-field span-12">
                    <label for="contrato">Contrato del Proveedor</label>
                    <div class="upload-box">
                        <input type="file" id="contrato" name="contrato" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <div class="upload-note"><strong>Formatos permitidos:</strong> PDF, DOC, DOCX, JPG, JPEG, PNG (Maximo 10MB)</div>
                        @if($current->contrato_path)
                        <div class="existing-link">Archivo actual: <a href="{{ route('admin.contabilidad.proveedores.contrato', $current) }}" target="_blank">Ver contrato</a></div>
                        @endif
                    </div>
                    @error('contrato') <div class="provider-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="provider-actions">
                @if($isEditing)
                <a href="{{ route('admin.contabilidad.proveedores') }}" class="btn-secondary">Cancelar edicion</a>
                @endif
                <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> {{ $isEditing ? 'Actualizar Proveedor' : 'Agregar Proveedor' }}</button>
            </div>
        </form>
    </article>

    <article class="module-card">
        <div class="module-head"><i class="fas fa-clipboard-list"></i><span>Lista de Proveedores</span></div>

        <div class="providers-table-shell">
            <div class="providers-table-wrap">
                <table class="providers-table">
                    <thead>
                        <tr>
                            <th>Empresa</th><th>RUC</th><th>Ubicacion</th><th>Categoria</th><th>Subcategoria</th><th>Contacto</th><th>Telefono</th><th>Descripcion</th><th>Yape/Plin</th><th>Cuenta Bancaria</th><th>Proximo Pago</th><th>Monto Total</th><th>Monto Pagado</th><th>Pendiente</th><th>Contrato</th><th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proveedores as $proveedor)
                        @php($style = \App\Support\ProveedorCatalog::estilo($proveedor->categoria))
                        <tr>
                            <td class="company-cell"><strong>{{ $proveedor->empresa }}</strong><span>{{ $proveedor->email ?: 'Sin email registrado' }}</span></td>
                            <td>{{ $proveedor->ruc }}</td>
                            <td><div class="location-text">{{ $proveedor->ubicacion_completa ?: '-' }}</div></td>
                            <td><span class="provider-badge" style="background:{{ $style['soft'] }};color:{{ $style['text'] }};"><i class="{{ $style['icon'] }}"></i> {{ $proveedor->categoria }}</span></td>
                            <td>{{ $proveedor->subcategoria }}</td>
                            <td>{{ $proveedor->persona_contacto ?: '-' }}</td>
                            <td>{{ $proveedor->telefono ?: '-' }}</td>
                            <td>{{ $proveedor->descripcion_servicio ?: '-' }}</td>
                            <td>@if($proveedor->yape_plin)<span class="digital-pill">{{ $proveedor->yape_plin }}</span>@else - @endif</td>
                            <td>{{ $proveedor->cuenta_bancaria ?: '-' }}</td>
                            <td>{{ $proveedor->proximo_pago?->format('d/m/Y') ?: '-' }}</td>
                            <td class="money-total">S/ {{ number_format((float) $proveedor->monto_total, 2, '.', ',') }}</td>
                            <td class="money-paid">S/ {{ number_format((float) $proveedor->monto_pagado, 2, '.', ',') }}</td>
                            <td class="money-pending">S/ {{ number_format((float) $proveedor->monto_pendiente, 2, '.', ',') }}</td>
                            <td>@if($proveedor->contrato_path)<a href="{{ route('admin.contabilidad.proveedores.contrato', $proveedor) }}" target="_blank" class="btn-secondary">Ver</a>@else - @endif</td>
                            <td>
                                <div style="display:grid;gap:8px;min-width:110px;">
                                    <a href="{{ route('admin.contabilidad.proveedores', ['edit' => $proveedor->id]) }}" class="action-btn edit"><i class="fas fa-pen"></i> Editar</a>
                                    <form method="POST" action="{{ route('admin.contabilidad.proveedores.destroy', $proveedor) }}" onsubmit="return confirm('Se eliminara el proveedor seleccionado.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete"><i class="fas fa-trash"></i> Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="16">
                                <div class="empty-state"><i class="fas fa-truck-field"></i><strong>Todavia no hay proveedores registrados.</strong><div style="margin-top:8px;">Empieza creando el primer proveedor desde el formulario superior.</div></div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="supplier-cards">
            @forelse($proveedores as $proveedor)
            @php($style = \App\Support\ProveedorCatalog::estilo($proveedor->categoria))
            <article class="supplier-card">
                <div class="supplier-card-head">
                    <div>
                        <strong>{{ $proveedor->empresa }}</strong>
                        <span>RUC: {{ $proveedor->ruc }}</span>
                    </div>

                    <div class="supplier-actions">
                        @if($proveedor->contrato_path)
                        <a href="{{ route('admin.contabilidad.proveedores.contrato', $proveedor) }}" target="_blank" class="action-btn view"><i class="fas fa-file-lines"></i> Contrato</a>
                        @endif
                        <a href="{{ route('admin.contabilidad.proveedores', ['edit' => $proveedor->id]) }}" class="action-btn edit"><i class="fas fa-pen"></i> Editar</a>
                        <form method="POST" action="{{ route('admin.contabilidad.proveedores.destroy', $proveedor) }}" onsubmit="return confirm('Se eliminara el proveedor seleccionado.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn delete"><i class="fas fa-trash"></i> Eliminar</button>
                        </form>
                    </div>
                </div>

                <div class="supplier-card-grid">
                    <div class="supplier-item"><span class="label">Categoria</span><span class="value"><span class="provider-badge" style="background:{{ $style['soft'] }};color:{{ $style['text'] }};"><i class="{{ $style['icon'] }}"></i> {{ $proveedor->categoria }}</span></span></div>
                    <div class="supplier-item"><span class="label">Subcategoria</span><span class="value">{{ $proveedor->subcategoria }}</span></div>
                    <div class="supplier-item"><span class="label">Contacto</span><span class="value">{{ $proveedor->persona_contacto ?: '-' }}</span></div>
                    <div class="supplier-item"><span class="label">Telefono</span><span class="value">{{ $proveedor->telefono ?: '-' }}</span></div>
                    <div class="supplier-item"><span class="label">Ubicacion</span><span class="value">{{ $proveedor->ubicacion_completa ?: '-' }}</span></div>
                    <div class="supplier-item"><span class="label">Descripcion</span><span class="value">{{ $proveedor->descripcion_servicio ?: '-' }}</span></div>
                    <div class="supplier-item"><span class="label">Yape/Plin</span><span class="value">{{ $proveedor->yape_plin ?: '-' }}</span></div>
                    <div class="supplier-item"><span class="label">Cuenta Bancaria</span><span class="value">{{ $proveedor->cuenta_bancaria ?: '-' }}</span></div>
                    <div class="supplier-item"><span class="label">Proximo Pago</span><span class="value">{{ $proveedor->proximo_pago?->format('d/m/Y') ?: '-' }}</span></div>
                    <div class="supplier-item"><span class="label">Monto Total</span><span class="value money-total">S/ {{ number_format((float) $proveedor->monto_total, 2, '.', ',') }}</span></div>
                    <div class="supplier-item"><span class="label">Monto Pagado</span><span class="value money-paid">S/ {{ number_format((float) $proveedor->monto_pagado, 2, '.', ',') }}</span></div>
                    <div class="supplier-item"><span class="label">Pendiente</span><span class="value money-pending">S/ {{ number_format((float) $proveedor->monto_pendiente, 2, '.', ',') }}</span></div>
                </div>
            </article>
            @empty
            <div class="empty-state"><i class="fas fa-truck-field"></i><strong>Todavia no hay proveedores registrados.</strong><div style="margin-top:8px;">Empieza creando el primer proveedor desde el formulario superior.</div></div>
            @endforelse
        </div>

        @if($proveedores->hasPages())
        <div class="pagination-row">
            <div class="muted">Mostrando {{ $proveedores->firstItem() }} a {{ $proveedores->lastItem() }} de {{ $proveedores->total() }} proveedores</div>
            <div class="provider-actions">
                <a href="{{ $proveedores->previousPageUrl() ?: '#' }}" class="btn-secondary {{ $proveedores->onFirstPage() ? 'disabled' : '' }}">Anterior</a>
                <a href="{{ $proveedores->hasMorePages() ? $proveedores->nextPageUrl() : '#' }}" class="btn-secondary {{ $proveedores->hasMorePages() ? '' : 'disabled' }}">Siguiente</a>
            </div>
        </div>
        @endif
    </article>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const categoriasMap = @json($subcategoriasPorCategoria);
        const categoriaSelect = document.getElementById('categoria_select');
        const categoriaNuevaInput = document.getElementById('categoria_nueva');
        const categoriaNuevaWrap = document.getElementById('categoriaNuevaWrap');
        const toggleCategoriaBtn = document.getElementById('toggleCategoriaBtn');
        const subcategoriaSelect = document.getElementById('subcategoria_select');
        const subcategoriaNuevaInput = document.getElementById('subcategoria_nueva');
        const subcategoriaNuevaWrap = document.getElementById('subcategoriaNuevaWrap');
        const toggleSubcategoriaBtn = document.getElementById('toggleSubcategoriaBtn');
        const montoTotal = document.getElementById('monto_total');
        const montoPagado = document.getElementById('monto_pagado');
        const pendingPreview = document.getElementById('pendingPreview');

        const formatCurrency = (value) => `S/ ${value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        const refreshPending = () => {
            if (!montoTotal || !montoPagado || !pendingPreview) return;
            const total = parseFloat(montoTotal.value || '0');
            const paid = parseFloat(montoPagado.value || '0');
            pendingPreview.textContent = formatCurrency(Math.max(total - paid, 0));
        };

        const refreshSubcategorias = () => {
            if (!categoriaSelect || !subcategoriaSelect) return;
            const selected = subcategoriaSelect.dataset.selected || '';
            const categoria = categoriaSelect.value;
            const opciones = categoriasMap[categoria] || [];
            subcategoriaSelect.innerHTML = opciones.length > 0 ? '<option value="">-- Seleccione subcategoria --</option>' : '<option value="">-- Primero seleccione categoria --</option>';
            opciones.forEach((item) => {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = item;
                option.selected = item === selected;
                subcategoriaSelect.appendChild(option);
            });
        };

        const syncCategoriaMode = () => {
            const customMode = Boolean(categoriaNuevaInput && categoriaNuevaInput.value.trim() !== '');
            categoriaNuevaWrap?.classList.toggle('active', customMode);
            if (categoriaSelect) categoriaSelect.disabled = customMode;
            if (toggleCategoriaBtn) toggleCategoriaBtn.innerHTML = customMode ? '<i class="fas fa-list"></i> Usar lista' : '<i class="fas fa-plus"></i> Nueva';
        };

        const syncSubcategoriaMode = () => {
            const customMode = Boolean(subcategoriaNuevaInput && subcategoriaNuevaInput.value.trim() !== '');
            subcategoriaNuevaWrap?.classList.toggle('active', customMode);
            if (subcategoriaSelect) subcategoriaSelect.disabled = customMode;
            if (toggleSubcategoriaBtn) toggleSubcategoriaBtn.innerHTML = customMode ? '<i class="fas fa-list"></i> Usar lista' : '<i class="fas fa-plus"></i> Nueva';
        };

        toggleCategoriaBtn?.addEventListener('click', () => {
            if (categoriaNuevaInput.value.trim() !== '') {
                categoriaNuevaInput.value = '';
            } else {
                categoriaSelect.value = '';
                categoriaNuevaInput.value = '';
                subcategoriaSelect.dataset.selected = '';
            }
            syncCategoriaMode();
            refreshSubcategorias();
        });

        toggleSubcategoriaBtn?.addEventListener('click', () => {
            if (subcategoriaNuevaInput.value.trim() !== '') {
                subcategoriaNuevaInput.value = '';
            } else {
                subcategoriaSelect.value = '';
                subcategoriaNuevaInput.value = '';
            }
            syncSubcategoriaMode();
        });

        categoriaSelect?.addEventListener('change', () => {
            subcategoriaSelect.dataset.selected = '';
            refreshSubcategorias();
        });

        montoTotal?.addEventListener('input', refreshPending);
        montoPagado?.addEventListener('input', refreshPending);

        refreshPending();
        syncCategoriaMode();
        syncSubcategoriaMode();
        refreshSubcategorias();
    });
</script>
@endpush
