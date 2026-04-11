@extends('layouts.admin-main', ['currentModule' => 'dashboard'])

@section('title', 'Dashboard Principal | BC Inmobiliaria')
@section('topbar_title')Dashboard <span>Principal</span>@endsection
@section('module_label', 'Dashboard Principal')
@section('page_title', 'Panel Principal')
@section('page_subtitle', 'Administra proyectos, accesos corporativos y la operación general del sistema inmobiliario desde un solo lugar.')
@section('page_actions')
<button type="button" class="btn-primary" onclick="openNuevoProyectoModal()">
    <i class="fas fa-plus"></i> Nuevo proyecto
</button>
@endsection

@php
    $proyectos         = $proyectos ?? collect();
    $ultimosPagos      = $ultimosPagos ?? collect();
    $clientesPorEstado = $clientesPorEstado ?? [];
    $ingresosMes       = $ingresosMes ?? 0;
    $egresosMes        = $egresosMes ?? 0;
    $proyectosActivos  = $proyectos->where('estado', 'activo')->count();
    $totalLotes        = (int) $proyectos->sum('lotes_count');
    $ticketPromedio    = $proyectos->isNotEmpty() ? $proyectos->avg('precio_base') : 0;
    $totalClientes     = array_sum($clientesPorEstado);
    $usuarioActual     = auth()->user();
    $formErrors        = $errors ?? new \Illuminate\Support\ViewErrorBag();
@endphp

@push('styles')
<style>
    .dashboard-grid{display:grid;grid-template-columns:1.45fr .95fr;gap:22px;align-items:start;}
    .hero-card{padding:28px;display:grid;gap:18px;background:linear-gradient(135deg,#1a1a2e 0%,#241457 45%,#5533CC 100%);color:#fff;position:relative;overflow:hidden;}
    .hero-card::before{content:'';position:absolute;right:-40px;top:-40px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(238,0,187,.22) 0%,transparent 72%);}
    .hero-card > *{position:relative;z-index:1;}
    .hero-chip-row{display:flex;gap:10px;flex-wrap:wrap;}
    .hero-chip{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:999px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.12);font-size:12px;font-weight:700;color:rgba(255,255,255,.82);}
    .hero-chip i{color:#ff9be8;}
    .hero-title{font-size:32px;font-weight:900;line-height:1.1;max-width:620px;}
    .hero-title span{color:#ff9be8;}
    .hero-copy{font-size:13px;line-height:1.75;color:rgba(255,255,255,.72);max-width:640px;}
    .hero-actions{display:flex;gap:12px;flex-wrap:wrap;}
    .hero-actions .btn-secondary{background:rgba(255,255,255,.1);color:#fff;border-color:rgba(255,255,255,.14);}
    .hero-actions .btn-secondary:hover{background:#fff;color:#5533CC;}
    .mini-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;}
    .mini-card{padding:18px 16px;border-radius:18px;background:#fff;border:1px solid var(--border);box-shadow:0 10px 24px rgba(15,23,42,.05);}
    .mini-card .label{font-size:11px;font-weight:800;letter-spacing:.7px;text-transform:uppercase;color:var(--gray);}
    .mini-card .value{margin-top:10px;font-size:28px;font-weight:900;line-height:1;color:var(--text);}
    .mini-card .helper{margin-top:6px;font-size:12px;color:var(--gray);}
    .project-grid{display:grid;gap:14px;}
    .project-item{display:grid;grid-template-columns:auto 1fr auto;gap:14px;align-items:center;padding:18px;border-radius:18px;border:1px solid var(--border);background:#fff;box-shadow:0 8px 22px rgba(15,23,42,.04);}
    .project-icon{width:52px;height:52px;border-radius:16px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;}
    .project-meta strong{display:block;font-size:15px;font-weight:800;color:var(--text);}
    .project-meta span{display:block;margin-top:4px;font-size:12px;color:var(--gray);}
    .project-stats{display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end;}
    .project-pill{display:inline-flex;align-items:center;gap:8px;padding:9px 12px;border-radius:999px;background:var(--bg);border:1px solid var(--border);font-size:12px;font-weight:700;color:var(--gray);}
    .project-pill.success{background:#ecfdf5;color:#15803d;border-color:#a7f3d0;}
    .project-pill.primary{background:#f5f3ff;color:#6d28d9;border-color:#ddd6fe;}
    .quick-links{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;}
    .quick-link{padding:18px;border-radius:18px;background:#fff;border:1px solid var(--border);box-shadow:0 8px 22px rgba(15,23,42,.04);text-decoration:none;color:inherit;display:grid;gap:10px;transition:.2s;}
    .quick-link:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(15,23,42,.08);}
    .quick-link.disabled{opacity:.55;pointer-events:none;cursor:not-allowed;}
    .quick-link .icon{width:46px;height:46px;border-radius:14px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;}
    .quick-link strong{font-size:14px;font-weight:800;color:var(--text);}
    .quick-link span{font-size:12px;line-height:1.7;color:var(--gray);}
    .empty-projects{padding:42px 18px;text-align:center;color:var(--gray);}
    /* ── Flujo de caja ── */
    .caja-row{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;}
    .caja-item{border-radius:14px;padding:14px 16px;}
    .caja-item.ing{background:#ecfdf5;border:1px solid #a7f3d0;}
    .caja-item.egr{background:#fff1f2;border:1px solid #fecdd3;}
    .caja-item .ci-label{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px;}
    .caja-item.ing .ci-label{color:#15803d;}
    .caja-item.egr .ci-label{color:#be123c;}
    .caja-item .ci-val{font-size:20px;font-weight:900;color:var(--text);}
    .caja-item .ci-sub{font-size:11px;color:var(--gray);margin-top:3px;}
    /* ── Clientes estado ── */
    .estado-list{display:grid;gap:8px;}
    .estado-row{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;background:var(--bg);}
    .estado-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;}
    .estado-name{font-size:13px;font-weight:700;color:var(--text);flex:1;}
    .estado-count{font-size:13px;font-weight:800;color:var(--text);}
    .estado-bar-wrap{width:80px;height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;}
    .estado-bar{height:100%;border-radius:999px;}
    /* ── Últimos pagos ── */
    .pago-list{display:grid;gap:8px;}
    .pago-row{display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:12px;background:var(--bg);}
    .pago-icon{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;}
    .pago-info{flex:1;min-width:0;}
    .pago-name{font-size:12px;font-weight:700;color:var(--text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
    .pago-sub{font-size:11px;color:var(--gray);}
    .pago-monto{font-size:13px;font-weight:800;color:var(--text);white-space:nowrap;}
    .empty-projects i{font-size:42px;display:block;margin-bottom:14px;opacity:.45;}
    .modal-overlay{position:fixed;inset:0;z-index:1200;background:rgba(15,23,42,.58);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center;padding:20px;}
    .modal-overlay.open{display:flex;}
    .modal-card{width:min(100%, 500px);background:#fff;border-radius:24px;border:1px solid var(--border);box-shadow:0 24px 60px rgba(15,23,42,.24);overflow:hidden;}
    .modal-head{padding:24px 24px 12px;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;}
    .modal-title{font-size:22px;font-weight:900;color:var(--text);line-height:1.2;}
    .modal-title span{color:var(--mg);}
    .modal-subtitle{margin-top:8px;font-size:12.5px;line-height:1.7;color:var(--gray);}
    .modal-close{width:40px;height:40px;border-radius:12px;border:1px solid var(--border);background:var(--bg);color:var(--gray);cursor:pointer;transition:.2s;}
    .modal-close:hover{background:#fff;color:var(--text);border-color:rgba(238,0,187,.25);}
    .modal-body{padding:0 24px 24px;}
    .modal-label{display:block;margin-bottom:8px;font-size:12px;font-weight:800;letter-spacing:.7px;text-transform:uppercase;color:var(--text);}
    .modal-label span{color:var(--mg);}
    .modal-input{width:100%;border:1.5px solid var(--border);background:var(--bg);border-radius:16px;padding:14px 16px;outline:none;font:500 14px 'Poppins',sans-serif;color:var(--text);transition:.2s;}
    .modal-input:focus{border-color:rgba(238,0,187,.35);background:#fff;box-shadow:0 0 0 4px rgba(238,0,187,.08);}
    .modal-note{margin-top:14px;padding:14px 16px;border-radius:16px;background:linear-gradient(135deg,rgba(238,0,187,.08),rgba(85,51,204,.08));font-size:12.5px;line-height:1.7;color:var(--gray);}
    .modal-actions{margin-top:18px;display:flex;justify-content:flex-end;gap:10px;flex-wrap:wrap;}
    .field-error{margin-top:8px;font-size:12px;font-weight:700;color:#be123c;}
    @media(max-width:1180px){.dashboard-grid{grid-template-columns:1fr;}.quick-links{grid-template-columns:repeat(2,minmax(0,1fr));}}
    @media(max-width:760px){.mini-grid,.quick-links{grid-template-columns:1fr;}.project-item{grid-template-columns:1fr;}.project-stats{justify-content:flex-start;}.hero-title{font-size:26px;}}
</style>
@endpush

@section('content')
<section class="dashboard-grid">
    <div style="display:grid;gap:22px;">
        <article class="card hero-card">
            <div class="hero-chip-row">
                <span class="hero-chip"><i class="fas fa-user-shield"></i> {{ $usuarioActual?->role?->nombre ?: 'Sin rol' }}</span>
                <span class="hero-chip"><i class="fas fa-layer-group"></i> {{ $proyectosActivos }} proyectos activos</span>
            </div>

            <div>
                <h2 class="hero-title">Bienvenido al <span>panel principal</span> del sistema inmobiliario</h2>
                <p class="hero-copy">Desde este espacio controlas la capa corporativa: proyectos, accesos administrativos y la operación general antes de entrar al detalle por proyecto.</p>
            </div>

            <div class="hero-actions">
                <button type="button" class="btn-primary" onclick="openNuevoProyectoModal()"><i class="fas fa-plus"></i> Crear proyecto</button>
                @if($usuarioActual?->isOwner())
                <a href="{{ route('admin.usuarios.index') }}" class="btn-secondary"><i class="fas fa-users-cog"></i> Usuarios del sistema</a>
                @endif
            </div>
        </article>

        <section class="card content-card">
            <div class="section-head">
                <div class="section-title">Proyectos <span>Disponibles</span></div>
            </div>

            <div class="project-grid">
                @forelse($proyectos as $proyecto)
                @php
                    $iconClasses = ['linear-gradient(135deg,#EE00BB,#C4009A)', 'linear-gradient(135deg,#5533CC,#3D1F99)', 'linear-gradient(135deg,#f59e0b,#d97706)'];
                    $iconNames = ['fas fa-home', 'fas fa-road', 'fas fa-location-dot'];
                    $colorIndex = $loop->index % 3;
                @endphp
                <article class="project-item">
                    <div class="project-icon" style="background:{{ $iconClasses[$colorIndex] }};">
                        <i class="{{ $iconNames[$colorIndex] }}"></i>
                    </div>

                    <div class="project-meta">
                        <strong>{{ $proyecto->nombre }}</strong>
                        <span>{{ $proyecto->ubicacion ?: 'Ubicacion por definir' }}</span>
                    </div>

                    <div class="project-stats">
                        <span class="project-pill primary">S/. {{ number_format((float) $proyecto->precio_base, 0, '.', ',') }}</span>
                        <span class="project-pill">{{ $proyecto->lotes_count ?? 0 }} lotes</span>
                        <span class="project-pill success">{{ ucfirst($proyecto->estado ?: 'activo') }}</span>
                        <a href="{{ route('admin.proyectos.dashboard', $proyecto) }}" class="btn-secondary"><i class="fas fa-eye"></i> Ver</a>
                    </div>
                </article>
                @empty
                <div class="empty-projects">
                    <i class="fas fa-building-circle-xmark"></i>
                    <strong>No hay proyectos registrados todavía.</strong>
                    <div style="margin-top:8px;">Crea el primer proyecto para habilitar su dashboard, lotes, clientes, cobranza, ingresos, egresos, caja y documentos.</div>
                </div>
                @endforelse
            </div>
        </section>
    </div>

    <div style="display:grid;gap:22px;">
        <section class="mini-grid">
            <article class="mini-card">
                <div class="label">Proyectos activos</div>
                <div class="value">{{ $proyectosActivos }}</div>
                <div class="helper">Estado corporativo actual</div>
            </article>
            <article class="mini-card">
                <div class="label">Total lotes</div>
                <div class="value">{{ $totalLotes }}</div>
                <div class="helper">Suma de proyectos visibles</div>
            </article>
            <article class="mini-card">
                <div class="label">Ticket promedio</div>
                <div class="value">S/. {{ number_format((float) $ticketPromedio, 0, '.', ',') }}</div>
                <div class="helper">Precio base promedio</div>
            </article>
            <article class="mini-card">
                <div class="label">Usuarios listos</div>
                <div class="value">{{ $usuarioActual?->role?->slug === 'dueno' ? 'OK' : 'ROL' }}</div>
                <div class="helper">Acceso corporativo actual</div>
            </article>
        </section>

        {{-- ── Flujo de caja del mes ── --}}
        <section class="card content-card" style="padding:20px 22px;">
            <div class="section-head" style="margin-bottom:14px;">
                <div class="section-title" style="font-size:15px;">Caja <span>del Mes</span></div>
                <span style="font-size:11px;color:var(--gray);font-weight:600;">{{ now()->translatedFormat('F Y') }}</span>
            </div>
            <div class="caja-row">
                <div class="caja-item ing">
                    <div class="ci-label"><i class="fas fa-arrow-down"></i> Ingresos</div>
                    <div class="ci-val">S/. {{ number_format((float)$ingresosMes, 0, '.', ',') }}</div>
                    <div class="ci-sub">Total registrado</div>
                </div>
                <div class="caja-item egr">
                    <div class="ci-label"><i class="fas fa-arrow-up"></i> Egresos</div>
                    <div class="ci-val">S/. {{ number_format((float)$egresosMes, 0, '.', ',') }}</div>
                    <div class="ci-sub">Total registrado</div>
                </div>
            </div>
            @php $balance = $ingresosMes - $egresosMes; @endphp
            <div style="padding:10px 14px;border-radius:12px;background:{{ $balance >= 0 ? '#ecfdf5' : '#fff1f2' }};display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;font-weight:700;color:var(--gray);">Balance neto</span>
                <span style="font-size:16px;font-weight:900;color:{{ $balance >= 0 ? '#15803d' : '#be123c' }};">
                    {{ $balance >= 0 ? '+' : '' }}S/. {{ number_format((float)$balance, 0, '.', ',') }}
                </span>
            </div>
        </section>

        {{-- ── Clientes por estado ── --}}
        <section class="card content-card" style="padding:20px 22px;">
            <div class="section-head" style="margin-bottom:14px;">
                <div class="section-title" style="font-size:15px;">Clientes <span>por Estado</span></div>
                <span style="font-size:11px;color:var(--gray);font-weight:600;">{{ $totalClientes }} total</span>
            </div>
            @php
                $estadoConfig = [
                    'reservado'      => ['color' => '#f59e0b', 'bg' => '#fef3c7', 'label' => 'Reservado'],
                    'financiamiento' => ['color' => '#2563eb', 'bg' => '#dbeafe', 'label' => 'Financiamiento'],
                    'pagado'         => ['color' => '#16a34a', 'bg' => '#dcfce7', 'label' => 'Pagado'],
                    'desistido'      => ['color' => '#dc2626', 'bg' => '#fee2e2', 'label' => 'Desistido'],
                ];
            @endphp
            <div class="estado-list">
                @foreach($estadoConfig as $key => $cfg)
                @php $count = $clientesPorEstado[$key] ?? 0; $pct = $totalClientes > 0 ? round($count / $totalClientes * 100) : 0; @endphp
                <div class="estado-row">
                    <div class="estado-dot" style="background:{{ $cfg['color'] }};"></div>
                    <div class="estado-name">{{ $cfg['label'] }}</div>
                    <div class="estado-bar-wrap">
                        <div class="estado-bar" style="width:{{ $pct }}%;background:{{ $cfg['color'] }};"></div>
                    </div>
                    <div class="estado-count">{{ $count }}</div>
                </div>
                @endforeach
            </div>
        </section>

        {{-- ── Últimos pagos ── --}}
        <section class="card content-card" style="padding:20px 22px;">
            <div class="section-head" style="margin-bottom:14px;">
                <div class="section-title" style="font-size:15px;">Últimos <span>Pagos</span></div>
            </div>
            @if($ultimosPagos->isEmpty())
            <div style="text-align:center;padding:20px;color:var(--gray);font-size:13px;">
                <i class="fas fa-receipt" style="font-size:28px;display:block;margin-bottom:8px;opacity:.4;"></i>
                Sin pagos registrados aún
            </div>
            @else
            <div class="pago-list">
                @foreach($ultimosPagos as $pago)
                @php
                    $iconMap = ['cuota'=>'fas fa-calendar-check','inicial'=>'fas fa-star','contado'=>'fas fa-money-bill','reserva'=>'fas fa-bookmark','ajuste_cuota'=>'fas fa-sliders'];
                    $bgMap   = ['cuota'=>'#dbeafe','inicial'=>'#f3e8ff','contado'=>'#dcfce7','reserva'=>'#fef3c7','ajuste_cuota'=>'#f3f4f6'];
                    $clrMap  = ['cuota'=>'#2563eb','inicial'=>'#7c3aed','contado'=>'#16a34a','reserva'=>'#d97706','ajuste_cuota'=>'#6b7280'];
                    $tp = $pago->tipo_pago;
                @endphp
                <div class="pago-row">
                    <div class="pago-icon" style="background:{{ $bgMap[$tp] ?? '#f3f4f6' }};color:{{ $clrMap[$tp] ?? '#6b7280' }};">
                        <i class="{{ $iconMap[$tp] ?? 'fas fa-receipt' }}"></i>
                    </div>
                    <div class="pago-info">
                        <div class="pago-name">{{ $pago->cliente?->nombre_completo ?? 'Cliente' }}</div>
                        <div class="pago-sub">{{ $pago->proyecto?->nombre }} · {{ optional($pago->fecha_pago)->format('d/m/Y') }}</div>
                    </div>
                    <div class="pago-monto">S/. {{ number_format((float)$pago->monto, 0, '.', ',') }}</div>
                </div>
                @endforeach
            </div>
            @endif
        </section>

    </div>
</section>

<section style="display:grid;gap:22px;margin-top:22px;">
    <section class="card content-card">
        <div class="section-head">
            <div class="section-title">Administracion <span>Central</span></div>
        </div>

        <div class="quick-links">
            @if($usuarioActual?->isOwner())
            <a href="{{ route('admin.usuarios.index') }}" class="quick-link">
                <div class="icon" style="background:linear-gradient(135deg,#64748b,#334155);">
                    <i class="fas fa-users-cog"></i>
                </div>
                <strong>Usuarios del Sistema</strong>
                <span>Gestiona accesos administrativos, roles base y estado de cuentas internas.</span>
            </a>
            @else
            <span class="quick-link disabled">
                <div class="icon" style="background:linear-gradient(135deg,#cbd5e1,#94a3b8);">
                    <i class="fas fa-users-cog"></i>
                </div>
                <strong>Usuarios del Sistema</strong>
                <span>Disponible para cuentas con perfil dueno del sistema.</span>
            </span>
            @endif

            <span class="quick-link disabled">
                <div class="icon" style="background:linear-gradient(135deg,#cbd5e1,#94a3b8);">
                    <i class="fas fa-user-shield"></i>
                </div>
                <strong>Gestion Permisos</strong>
                <span>Reservado para la futura matriz de permisos por modulo y trazabilidad.</span>
            </span>
        </div>
    </section>

    <section class="card content-card">
        <div class="section-head">
            <div class="section-title">Contabilidad <span>Corporativa</span></div>
        </div>

        <div class="quick-links">
            <a href="{{ route('admin.contabilidad.general') }}" class="quick-link">
                <div class="icon" style="background:linear-gradient(135deg,#111827,#5533CC);">
                    <i class="fas fa-calculator"></i>
                </div>
                <strong>Contabilidad General</strong>
                <span>Abre la vista global para balances, resumen financiero y lectura ejecutiva corporativa.</span>
            </a>

            <a href="{{ route('admin.contabilidad.datos') }}" class="quick-link">
                <div class="icon" style="background:linear-gradient(135deg,#0f172a,#0ea5e9);">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <strong>Datos Contables</strong>
                <span>Centraliza catalogos, parametros y estructuras base para la operacion financiera.</span>
            </a>

            <a href="{{ route('admin.contabilidad.planilla') }}" class="quick-link">
                <div class="icon" style="background:linear-gradient(135deg,#5f2c82,#ee00bb);">
                    <i class="fas fa-users"></i>
                </div>
                <strong>Planilla</strong>
                <span>Prepara el flujo de personal, conceptos de pago y salida contable relacionada.</span>
            </a>

            <a href="{{ route('admin.contabilidad.proveedores') }}" class="quick-link">
                <div class="icon" style="background:linear-gradient(135deg,#92400e,#f59e0b);">
                    <i class="fas fa-truck"></i>
                </div>
                <strong>Proveedores</strong>
                <span>Controla terceros, documentos, obligaciones pendientes y cuentas por pagar.</span>
            </a>
        </div>
    </section>
</section>

<div class="modal-overlay{{ $formErrors->has('nombre') ? ' open' : '' }}" id="nuevoProyectoModal">
    <div class="modal-card">
        <div class="modal-head">
            <div>
                <div class="modal-title">Crear <span>Nuevo Proyecto</span></div>
                <div class="modal-subtitle">Ingresa el nombre del proyecto. El sistema generará automáticamente su panel y los módulos base.</div>
            </div>
            <button type="button" class="modal-close" onclick="closeNuevoProyectoModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('admin.proyectos.store') }}" id="nuevoProyectoForm">
                @csrf

                <label for="nuevoProyectoNombre" class="modal-label">Nombre del proyecto <span>*</span></label>
                <input
                    type="text"
                    id="nuevoProyectoNombre"
                    name="nombre"
                    class="modal-input"
                    placeholder="Ej: Residencial Las Lomas"
                    value="{{ old('nombre') }}"
                    maxlength="150"
                    required
                >

                @if($formErrors->has('nombre'))
                <div class="field-error">{{ $formErrors->first('nombre') }}</div>
                @endif

                <div class="modal-note">
                    Se crearán automáticamente el código, slug, panel del proyecto y la estructura base para lotes, clientes, cobranza, ingresos, egresos, caja y documentos.
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeNuevoProyectoModal()">Cancelar</button>
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Crear proyecto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const nuevoProyectoModal = document.getElementById('nuevoProyectoModal');
    const nuevoProyectoNombre = document.getElementById('nuevoProyectoNombre');

    function openNuevoProyectoModal() {
        if (!nuevoProyectoModal) {
            return;
        }

        nuevoProyectoModal.classList.add('open');

        if (nuevoProyectoNombre) {
            setTimeout(() => nuevoProyectoNombre.focus(), 40);
        }
    }

    function closeNuevoProyectoModal() {
        if (!nuevoProyectoModal) {
            return;
        }

        nuevoProyectoModal.classList.remove('open');
    }

    if (nuevoProyectoModal) {
        nuevoProyectoModal.addEventListener('click', (event) => {
            if (event.target === nuevoProyectoModal) {
                closeNuevoProyectoModal();
            }
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeNuevoProyectoModal();
        }
    });
</script>
@endpush
