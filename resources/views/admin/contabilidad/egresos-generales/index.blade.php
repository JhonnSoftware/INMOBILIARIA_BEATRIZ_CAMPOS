@extends('layouts.admin-main', ['currentModule' => 'egresos-generales'])

@section('title', 'Egresos Generales | BC Inmobiliaria')
@section('topbar_title')Egresos <span>Generales</span>@endsection
@section('module_label', 'Egresos Generales')
@section('page_title', 'Egresos Generales')
@section('page_subtitle', 'Visualiza y filtra todos los egresos registrados por proyecto.')
@section('page_actions')
<a href="{{ route('admin.dashboard') }}" class="btn-secondary"><i class="fas fa-arrow-left"></i> Panel principal</a>
@endsection

@push('styles')
<style>
    .eg-page{display:grid;gap:22px;min-width:0;}

    /* Hero selector */
    .eg-hero{background:linear-gradient(135deg,#5533cc,#ee00bb);border-radius:22px;padding:24px 28px;display:grid;grid-template-columns:1fr auto;gap:20px;align-items:center;color:#fff;min-width:0;}
    .eg-hero-left h2{font-size:22px;font-weight:900;line-height:1.1;}
    .eg-hero-left p{margin-top:6px;font-size:13px;opacity:.82;}
    .eg-hero-total{font-size:32px;font-weight:900;white-space:nowrap;}
    .eg-hero-total small{display:block;font-size:12px;font-weight:600;opacity:.75;text-align:right;margin-top:4px;}

    /* Selector de proyecto */
    .eg-selector{background:#fff;border-radius:18px;padding:18px 22px;border:1.5px solid var(--border);box-shadow:0 4px 18px rgba(85,51,204,.06);display:grid;grid-template-columns:1fr 1fr auto;gap:14px;align-items:end;}
    .eg-selector label{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:var(--gray);display:block;margin-bottom:6px;}
    .eg-selector select{width:100%;border:1.5px solid var(--border);border-radius:12px;padding:11px 14px;font:600 13px 'Poppins',sans-serif;color:var(--text);background:#fff;outline:none;}
    .eg-selector select:focus{border-color:rgba(85,51,204,.3);box-shadow:0 0 0 3px rgba(85,51,204,.08);}

    /* Stat cards */
    .eg-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;}
    .eg-stat{background:#fff;border-radius:16px;padding:18px 16px;border:1.5px solid var(--border);border-left:4px solid var(--stat-color);}
    .eg-stat .label{font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:var(--gray);}
    .eg-stat .value{margin-top:8px;font-size:22px;font-weight:900;color:var(--stat-color);line-height:1;}
    .eg-stat .helper{margin-top:5px;font-size:11px;color:var(--gray);line-height:1.5;}

    /* Layout principal */
    .eg-layout{display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:start;}

    /* Filtros */
    .eg-filters{background:#fff;border-radius:18px;padding:20px;border:1.5px solid var(--border);box-shadow:0 4px 16px rgba(15,23,42,.04);display:grid;gap:14px;}
    .eg-filters-title{font-size:13px;font-weight:800;color:var(--text);display:flex;align-items:center;gap:8px;padding-bottom:12px;border-bottom:1px solid var(--border);}
    .eg-filters-title i{color:var(--vt);}
    .eg-filter-field{display:grid;gap:6px;}
    .eg-filter-field label{font-size:11px;font-weight:800;color:var(--gray);text-transform:uppercase;letter-spacing:.5px;}
    .eg-filter-field select,.eg-filter-field input{width:100%;border:1.5px solid var(--border);border-radius:10px;padding:10px 12px;font:500 12px 'Poppins',sans-serif;color:var(--text);background:#fff;outline:none;}
    .eg-filter-field select:focus,.eg-filter-field input:focus{border-color:rgba(85,51,204,.3);box-shadow:0 0 0 3px rgba(85,51,204,.08);}
    .eg-filter-row{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
    .eg-filter-actions{display:grid;gap:8px;margin-top:4px;}

    /* Categorías sidebar */
    .eg-cat-box{background:#fff;border-radius:18px;padding:18px 20px;border:1.5px solid var(--border);box-shadow:0 4px 16px rgba(15,23,42,.04);}
    .eg-cat-title{font-size:12px;font-weight:800;color:var(--text);text-transform:uppercase;letter-spacing:.6px;padding-bottom:10px;border-bottom:1px solid var(--border);margin-bottom:12px;}
    .eg-cat-item{display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f1f3f9;}
    .eg-cat-item:last-child{border-bottom:none;}
    .eg-cat-item span{font-size:12px;font-weight:700;color:var(--text);}
    .eg-cat-item strong{font-size:12px;font-weight:900;color:var(--vt);}
    .eg-cat-bar{height:3px;border-radius:999px;background:var(--border);margin-top:4px;overflow:hidden;}
    .eg-cat-bar-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,#5533cc,#ee00bb);}

    /* Fuentes */
    .eg-fuente-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;background:var(--bg);border:1px solid var(--border);margin-bottom:8px;}
    .eg-fuente-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;}
    .eg-fuente-info{flex:1;min-width:0;}
    .eg-fuente-info span{display:block;font-size:11.5px;font-weight:700;color:var(--text);}
    .eg-fuente-info small{display:block;font-size:11px;color:var(--gray);}
    .eg-fuente-monto{font-size:13px;font-weight:900;color:var(--text);}

    /* Lista egresos */
    .eg-list{display:grid;gap:12px;}
    .eg-card{background:#fff;border-radius:16px;border:1.5px solid var(--border);box-shadow:0 4px 14px rgba(15,23,42,.04);overflow:hidden;transition:.18s;}
    .eg-card:hover{box-shadow:0 8px 22px rgba(15,23,42,.08);transform:translateY(-1px);}
    .eg-card-head{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;padding:14px 16px;border-bottom:1px solid #f1f3f9;}
    .eg-card-accent{width:4px;border-radius:4px;align-self:stretch;}
    .eg-card-meta{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
    .eg-badge{display:inline-flex;align-items:center;padding:5px 11px;border-radius:999px;font-size:11px;font-weight:800;}
    .eg-badge.marketing{background:#fce7f3;color:#be185d;}
    .eg-badge.administrativo{background:#dbeafe;color:#1d4ed8;}
    .eg-badge.ventas{background:#dcfce7;color:#15803d;}
    .eg-badge.terreno{background:#fef3c7;color:#92400e;}
    .eg-badge.proyectos{background:#f3e8ff;color:#7c3aed;}
    .eg-badge.otros{background:#f1f5f9;color:#475569;}
    .eg-badge.fuente{background:#f0fdf4;color:#166534;}
    .eg-badge.anulado{background:#fee2e2;color:#dc2626;}
    .eg-date{font-size:11.5px;color:var(--gray);display:flex;align-items:center;gap:5px;}
    .eg-responsable{font-size:11.5px;font-weight:700;color:var(--text);display:flex;align-items:center;gap:5px;}
    .eg-monto{font-size:18px;font-weight:900;color:#16a34a;white-space:nowrap;}
    .eg-monto.anulado{color:#94a3b8;text-decoration:line-through;}
    .eg-card-body{padding:12px 16px;display:grid;grid-template-columns:1fr auto;gap:12px;align-items:center;}
    .eg-desc{font-size:12.5px;color:var(--text);line-height:1.6;}
    .eg-desc small{display:block;margin-top:3px;font-size:11px;color:var(--gray);}
    .eg-actions{display:flex;gap:8px;align-items:center;}
    .eg-files-badge{display:inline-flex;align-items:center;gap:5px;padding:5px 10px;border-radius:8px;background:#f3f0ff;color:var(--vt);font-size:11px;font-weight:700;text-decoration:none;}
    .eg-files-badge:hover{background:#ede8ff;}
    .eg-edit-btn{display:inline-flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;background:#f8fafc;border:1px solid var(--border);color:var(--gray);font-size:11px;font-weight:700;text-decoration:none;transition:.15s;}
    .eg-edit-btn:hover{border-color:var(--vt);color:var(--vt);}

    /* Tabla en pantallas grandes */
    .eg-table-shell{display:none;}
    .eg-table-wrap{overflow-x:auto;border-radius:16px;border:1.5px solid var(--border);}
    .eg-table{width:100%;border-collapse:separate;border-spacing:0;}
    .eg-table thead th{background:linear-gradient(135deg,#5533cc,#7c3aed);color:#fff;padding:13px 14px;font-size:10.5px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;text-align:left;white-space:nowrap;}
    .eg-table tbody td{padding:14px;font-size:12.5px;color:var(--text);border-bottom:1px solid #f1f3f9;vertical-align:middle;}
    .eg-table tbody tr:last-child td{border-bottom:none;}
    .eg-table tbody tr:hover td{background:#faf8ff;}

    /* Empty */
    .eg-empty{padding:48px 20px;text-align:center;color:var(--gray);background:#fff;border-radius:16px;border:1.5px solid var(--border);}
    .eg-empty i{font-size:40px;display:block;margin-bottom:14px;opacity:.3;}

    /* Pagination */
    .eg-pagination{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-top:16px;}
    .eg-pagination .muted{font-size:12px;color:var(--gray);}

    @media(max-width:1200px){.eg-layout{grid-template-columns:1fr;}.eg-sidebar{display:grid;grid-template-columns:1fr 1fr;gap:16px;}}
    @media(max-width:860px){.eg-stats{grid-template-columns:repeat(2,minmax(0,1fr));}.eg-hero{grid-template-columns:1fr;}.eg-selector{grid-template-columns:1fr;}.eg-sidebar{grid-template-columns:1fr;}}
    @media(max-width:560px){.eg-stats{grid-template-columns:1fr;}.eg-card-head{grid-template-columns:auto 1fr;}.eg-monto{font-size:15px;}}
</style>
@endpush

@section('content')
<section class="eg-page">

    {{-- HERO con total del proyecto --}}
    <div class="eg-hero">
        <div class="eg-hero-left">
            <h2><i class="fas fa-file-invoice-dollar"></i> {{ $proyectoActual?->nombre ?? 'Sin proyecto' }}</h2>
            <p>Egresos registrados · Filtra por proyecto, categoría, fuente o fecha</p>
        </div>
        <div class="eg-hero-total">
            S/ {{ number_format((float) $resumen['total_general'], 2, '.', ',') }}
            <small>Total acumulado</small>
        </div>
    </div>

    {{-- SELECTOR DE PROYECTO + FILTRO RÁPIDO --}}
    <form method="GET" action="{{ route('admin.contabilidad.egresos-generales') }}" id="selectorForm">
        <div class="eg-selector">
            <div>
                <label for="proyecto_id">Proyecto</label>
                <select name="proyecto_id" id="proyecto_id" onchange="document.getElementById('selectorForm').submit()">
                    @foreach($proyectos as $p)
                    <option value="{{ $p->id }}" @selected($proyectoActual?->id === $p->id)>{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="estado">Estado</label>
                <select name="estado" id="estado">
                    <option value="">Todos los estados</option>
                    @foreach($catalogoEstados as $key => $label)
                    <option value="{{ $key }}" @selected($estado === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="opacity:0">Accion</label>
                <button type="submit" class="btn-primary" style="width:100%;justify-content:center;"><i class="fas fa-filter"></i> Filtrar</button>
            </div>
        </div>
    </form>

    {{-- STAT CARDS --}}
    <div class="eg-stats">
        <div class="eg-stat" style="--stat-color:#ef4444;">
            <div class="label">Total General</div>
            <div class="value">S/ {{ number_format((float) $resumen['total_general'], 2, '.', ',') }}</div>
            <div class="helper">Suma acumulada registrada</div>
        </div>
        <div class="eg-stat" style="--stat-color:#f59e0b;">
            <div class="label">Mes Actual</div>
            <div class="value">S/ {{ number_format((float) $resumen['total_mes'], 2, '.', ',') }}</div>
            <div class="helper">{{ now()->translatedFormat('F Y') }}</div>
        </div>
        <div class="eg-stat" style="--stat-color:#5533cc;">
            <div class="label">N° Egresos</div>
            <div class="value">{{ $resumen['total_egresos'] }}</div>
            <div class="helper">Registros activos</div>
        </div>
        <div class="eg-stat" style="--stat-color:#64748b;">
            <div class="label">Sin Comprobante</div>
            <div class="value">{{ $resumen['sin_comprobante'] }}</div>
            <div class="helper">Pendientes de documentar</div>
        </div>
    </div>

    {{-- LAYOUT PRINCIPAL --}}
    <div class="eg-layout">

        {{-- LISTA EGRESOS --}}
        <div>
            {{-- Filtros avanzados --}}
            <form method="GET" action="{{ route('admin.contabilidad.egresos-generales') }}" style="margin-bottom:16px;">
                <input type="hidden" name="proyecto_id" value="{{ $proyectoActual?->id }}">
                <div class="eg-filters">
                    <div class="eg-filters-title"><i class="fas fa-sliders"></i> Filtros avanzados</div>
                    <div class="eg-filter-row">
                        <div class="eg-filter-field">
                            <label>Categoría Principal</label>
                            <select name="categoria_principal" id="filtroCatPrincipal" onchange="updateSubcat()">
                                <option value="">Todas</option>
                                @foreach(array_keys($catalogoCategorias) as $cp)
                                <option value="{{ $cp }}" @selected($catPrincipal === $cp)>{{ $cp }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="eg-filter-field">
                            <label>Subcategoría</label>
                            <select name="categoria" id="filtroCategoria">
                                <option value="">Todas</option>
                                @foreach($catalogoCategorias as $cp => $subs)
                                @foreach($subs as $sub)
                                <option value="{{ $sub }}" data-parent="{{ $cp }}" @selected($categoria === $sub)>{{ $sub }}</option>
                                @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="eg-filter-row">
                        <div class="eg-filter-field">
                            <label>Fuente de Dinero</label>
                            <select name="fuente_dinero">
                                <option value="">Todas</option>
                                @foreach($catalogoFuentes as $key => $label)
                                <option value="{{ $key }}" @selected($fuente === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="eg-filter-field">
                            <label>Responsable</label>
                            <input type="text" name="responsable" value="{{ $responsable }}" placeholder="Buscar...">
                        </div>
                    </div>
                    <div class="eg-filter-row">
                        <div class="eg-filter-field">
                            <label>Mes</label>
                            <select name="mes">
                                <option value="">Todos</option>
                                @foreach(range(1,12) as $m)
                                <option value="{{ $m }}" @selected($mes === $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="eg-filter-field">
                            <label>Año</label>
                            <select name="anio">
                                <option value="">Todos</option>
                                @foreach(range(now()->year, 2023) as $y)
                                <option value="{{ $y }}" @selected($anio === $y)>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="eg-filter-actions">
                        <button type="submit" class="btn-primary" style="justify-content:center;"><i class="fas fa-filter"></i> Aplicar filtros</button>
                        <a href="{{ route('admin.contabilidad.egresos-generales', ['proyecto_id' => $proyectoActual?->id]) }}" class="btn-secondary" style="justify-content:center;text-decoration:none;"><i class="fas fa-times"></i> Limpiar</a>
                    </div>
                </div>
            </form>

            {{-- Lista de egresos --}}
            @if($egresos->isEmpty())
            <div class="eg-empty">
                <i class="fas fa-file-invoice-dollar"></i>
                <strong>No hay egresos con los filtros aplicados</strong>
                <p style="margin-top:8px;font-size:13px;">Cambia el proyecto o ajusta los filtros.</p>
            </div>
            @else
            <div class="eg-list">
                @foreach($egresos as $egreso)
                @php
                    $colorMap = [
                        'Marketing' => '#be185d',
                        'Administrativo' => '#1d4ed8',
                        'Ventas' => '#15803d',
                        'Terreno' => '#92400e',
                        'Proyectos' => '#7c3aed',
                        'Otros' => '#475569',
                    ];
                    $badgeMap = [
                        'Marketing' => 'marketing',
                        'Administrativo' => 'administrativo',
                        'Ventas' => 'ventas',
                        'Terreno' => 'terreno',
                        'Proyectos' => 'proyectos',
                        'Otros' => 'otros',
                    ];
                    $color = $colorMap[$egreso->categoria_principal] ?? '#475569';
                    $badge = $badgeMap[$egreso->categoria_principal] ?? 'otros';
                    $esAnulado = $egreso->estado === 'anulado';
                @endphp
                <div class="eg-card">
                    <div class="eg-card-head">
                        <div class="eg-card-accent" style="background:{{ $color }};"></div>
                        <div class="eg-card-meta">
                            <span class="eg-badge {{ $badge }}">{{ $egreso->categoria_principal }}</span>
                            @if($egreso->categoria)
                            <span class="eg-badge otros">{{ $egreso->categoria }}</span>
                            @endif
                            @if($egreso->fuente_dinero)
                            <span class="eg-badge fuente"><i class="fas fa-wallet" style="font-size:9px;"></i> {{ \App\Support\EgresoCatalog::etiquetaFuente($egreso->fuente_dinero) }}</span>
                            @endif
                            @if($esAnulado)
                            <span class="eg-badge anulado">Anulado</span>
                            @endif
                            <span class="eg-date"><i class="fas fa-calendar-alt"></i> {{ $egreso->fecha->format('d/m/Y') }}</span>
                            @if($egreso->responsable)
                            <span class="eg-responsable"><i class="fas fa-user"></i> {{ $egreso->responsable }}</span>
                            @endif
                        </div>
                        <div class="eg-monto {{ $esAnulado ? 'anulado' : '' }}">
                            S/ {{ number_format((float) $egreso->monto, 2, '.', ',') }}
                        </div>
                    </div>
                    <div class="eg-card-body">
                        <div class="eg-desc">
                            {{ $egreso->descripcion ?: 'Sin descripción' }}
                            @if($egreso->razon_social)
                            <small><i class="fas fa-building" style="font-size:10px;"></i> {{ $egreso->razon_social }} @if($egreso->ruc_proveedor) · RUC: {{ $egreso->ruc_proveedor }} @endif</small>
                            @endif
                            @if($egreso->observaciones)
                            <small><i class="fas fa-comment" style="font-size:10px;"></i> {{ $egreso->observaciones }}</small>
                            @endif
                        </div>
                        <div class="eg-actions">
                            @if($egreso->archivos_count > 0)
                            <a href="{{ route('admin.proyectos.egresos', $egreso->proyecto) }}" class="eg-files-badge">
                                <i class="fas fa-paperclip"></i> {{ $egreso->archivos_count }}
                            </a>
                            @endif
                            <a href="{{ route('admin.proyectos.egresos.edit', [$egreso->proyecto, $egreso]) }}" class="eg-edit-btn">
                                <i class="fas fa-pen"></i> Editar
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="eg-pagination">
                <span class="muted">{{ $egresos->firstItem() }}–{{ $egresos->lastItem() }} de {{ $egresos->total() }} registros</span>
                {{ $egresos->links() }}
            </div>
            @endif
        </div>

        {{-- SIDEBAR: Categorías + Fuentes --}}
        <div class="eg-sidebar" style="display:grid;gap:16px;">
            {{-- Por categoría --}}
            <div class="eg-cat-box">
                <div class="eg-cat-title"><i class="fas fa-chart-pie" style="color:var(--vt);margin-right:6px;"></i> Por Categoría</div>
                @php $maxCat = max(array_values($totalesPorCategoria) ?: [1]); @endphp
                @forelse($totalesPorCategoria as $cat => $total)
                <div class="eg-cat-item">
                    <span>{{ $cat ?: 'Sin categoría' }}</span>
                    <strong>S/ {{ number_format((float) $total, 0, '.', ',') }}</strong>
                </div>
                <div class="eg-cat-bar">
                    <div class="eg-cat-bar-fill" style="width:{{ $maxCat > 0 ? round(($total / $maxCat) * 100) : 0 }}%;"></div>
                </div>
                @empty
                <p style="font-size:12px;color:var(--gray);text-align:center;padding:12px 0;">Sin datos</p>
                @endforelse
            </div>

            {{-- Por fuente de dinero --}}
            <div class="eg-cat-box">
                <div class="eg-cat-title"><i class="fas fa-wallet" style="color:#16a34a;margin-right:6px;"></i> Por Fuente</div>
                @php
                    $fuenteColors = ['caja_personal' => '#7c3aed', 'caja_chica' => '#ec4899', 'caja_general' => '#2563eb'];
                @endphp
                @forelse($totalesPorFuente as $fuente_key => $total)
                <div class="eg-fuente-item">
                    <div class="eg-fuente-dot" style="background:{{ $fuenteColors[$fuente_key] ?? '#94a3b8' }};"></div>
                    <div class="eg-fuente-info">
                        <span>{{ \App\Support\EgresoCatalog::etiquetaFuente($fuente_key) }}</span>
                        <small>{{ $fuente_key }}</small>
                    </div>
                    <div class="eg-fuente-monto">S/ {{ number_format((float) $total, 0, '.', ',') }}</div>
                </div>
                @empty
                <p style="font-size:12px;color:var(--gray);text-align:center;padding:12px 0;">Sin datos</p>
                @endforelse
            </div>

            {{-- Acceso rápido al proyecto --}}
            @if($proyectoActual)
            <div class="eg-cat-box">
                <div class="eg-cat-title"><i class="fas fa-link" style="color:#f59e0b;margin-right:6px;"></i> Accesos Rápidos</div>
                <a href="{{ route('admin.proyectos.egresos', $proyectoActual) }}" class="eg-edit-btn" style="display:flex;margin-bottom:8px;">
                    <i class="fas fa-file-invoice-dollar"></i> Egresos del proyecto
                </a>
                <a href="{{ route('admin.proyectos.dashboard', $proyectoActual) }}" class="eg-edit-btn" style="display:flex;">
                    <i class="fas fa-building"></i> Dashboard del proyecto
                </a>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
const catalogoCategorias = @json($catalogoCategorias);

function updateSubcat() {
    const principal = document.getElementById('filtroCatPrincipal').value;
    const select = document.getElementById('filtroCategoria');
    const current = select.value;
    const options = select.querySelectorAll('option[data-parent]');

    options.forEach(opt => {
        const match = !principal || opt.dataset.parent === principal;
        opt.style.display = match ? '' : 'none';
    });

    if (!select.querySelector(`option[value="${current}"]:not([style*="none"])`)) {
        select.value = '';
    }
}

document.addEventListener('DOMContentLoaded', updateSubcat);
</script>
@endpush
