<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $proyecto->nombre }} — BC Inmobiliaria</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
        :root{
            --mg:#EE00BB;
            --mg2:#C4009A;
            --vt:#5533CC;
            --vt2:#3D1F99;
            --dark:#1a1a2e;
            --dark2:#16213e;
            --bg:#f0f2ff;
            --white:#ffffff;
            --border:#e8eaf6;
            --gray:#64748b;
            --text:#1a1a2e;
            --gn:#10b981;
            --gn2:#059669;
            --yw:#f59e0b;
            --yw2:#d97706;
            --bl:#3b82f6;
            --bl2:#1d4ed8;
            --re:#ef4444;
            --re2:#dc2626;
            --navbar-h:64px;
        }
        html,body{height:100%;font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}

        /* ===== NAVBAR ===== */
        .navbar{
            height:var(--navbar-h);
            background:var(--dark);
            display:flex;align-items:center;
            padding:0 28px;
            position:sticky;top:0;z-index:200;
            box-shadow:0 2px 16px rgba(0,0,0,.25);
            gap:0;
        }
        .nav-brand{
            display:flex;align-items:center;gap:10px;
            text-decoration:none;margin-right:32px;flex-shrink:0;
        }
        .nav-logo{
            width:40px;height:40px;border-radius:11px;
            background:linear-gradient(135deg,var(--mg),var(--vt));
            display:flex;align-items:center;justify-content:center;
            overflow:hidden;
            box-shadow:0 4px 14px rgba(238,0,187,.35);
        }
        .nav-logo img{width:100%;height:100%;object-fit:contain;padding:3px;}
        .nav-brand-text{display:flex;flex-direction:column;line-height:1.1;}
        .nav-brand-text .nb-name{font-size:13px;font-weight:800;color:#fff;}
        .nav-brand-text .nb-sub{font-size:9px;font-weight:600;color:var(--mg);letter-spacing:2px;text-transform:uppercase;}

        .nav-links{display:flex;align-items:center;gap:4px;flex:1;}
        .nav-link{
            display:flex;align-items:center;gap:7px;
            padding:8px 14px;border-radius:10px;
            text-decoration:none;color:rgba(255,255,255,.6);
            font-size:12.5px;font-weight:500;
            transition:all .2s;white-space:nowrap;
        }
        .nav-link:hover{color:#fff;background:rgba(255,255,255,.07);}
        .nav-link.active{
            color:#fff;
            background:linear-gradient(135deg,rgba(238,0,187,.2),rgba(85,51,204,.2));
        }
        .nav-link i{font-size:13px;}

        .nav-right{display:flex;align-items:center;gap:10px;margin-left:auto;flex-shrink:0;}
        .nav-back{
            display:flex;align-items:center;gap:7px;
            padding:8px 16px;border-radius:10px;
            background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);
            color:rgba(255,255,255,.7);text-decoration:none;
            font-size:12px;font-weight:600;transition:.2s;
        }
        .nav-back:hover{background:rgba(255,255,255,.12);color:#fff;}

        /* ===== PAGE WRAPPER ===== */
        .page-wrap{padding:28px 32px;max-width:1500px;margin:0 auto;}

        /* ===== PAGE HEADER ===== */
        .page-header{
            background:linear-gradient(135deg,var(--dark2) 0%,var(--dark) 40%,#2d1b69 70%,var(--vt2) 100%);
            border-radius:20px;padding:26px 32px;
            display:flex;align-items:center;justify-content:space-between;
            margin-bottom:26px;position:relative;overflow:hidden;
        }
        .page-header::before{
            content:'';position:absolute;right:-40px;top:-40px;
            width:220px;height:220px;border-radius:50%;
            background:radial-gradient(circle,rgba(238,0,187,.18) 0%,transparent 70%);
        }
        .ph-left{}
        .ph-breadcrumb{display:flex;align-items:center;gap:8px;margin-bottom:6px;}
        .ph-breadcrumb a{font-size:12px;color:rgba(255,255,255,.5);text-decoration:none;transition:.2s;}
        .ph-breadcrumb a:hover{color:rgba(255,255,255,.8);}
        .ph-breadcrumb .sep{color:rgba(255,255,255,.2);font-size:11px;}
        .ph-breadcrumb .current{font-size:12px;color:rgba(255,255,255,.8);}
        .ph-title{font-size:22px;font-weight:800;color:#fff;margin-bottom:4px;}
        .ph-title em{font-style:normal;color:var(--mg);}
        .ph-subtitle{font-size:13px;color:rgba(255,255,255,.55);}
        .ph-right{display:flex;align-items:center;gap:10px;position:relative;z-index:1;}
        .ph-badge{
            padding:6px 16px;border-radius:50px;
            font-size:12px;font-weight:700;
            background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);
            color:#fff;display:flex;align-items:center;gap:6px;
        }
        .ph-badge .dot{width:8px;height:8px;border-radius:50%;background:var(--gn);}

        /* ===== MAIN GRID ===== */
        .main-grid{
            display:grid;
            grid-template-columns:300px 1fr;
            gap:22px;
            align-items:start;
        }

        /* ===== PANEL IZQUIERDO — ESTADO DE LOTES ===== */
        .panel-estado{
            background:var(--white);border-radius:18px;
            border:1px solid var(--border);
            box-shadow:0 2px 12px rgba(0,0,0,.05);
            overflow:hidden;
            position:sticky;top:calc(var(--navbar-h) + 16px);
        }
        .pe-head{
            padding:18px 22px 14px;
            border-bottom:1px solid var(--border);
        }
        .pe-title{font-size:14px;font-weight:700;color:var(--text);}
        .pe-title span{color:var(--mg);}
        .pe-subtitle{font-size:11.5px;color:var(--gray);margin-top:2px;}

        .pe-cards{padding:16px 16px 10px;}
        .pe-card{
            border-radius:14px;padding:14px 16px;
            display:flex;align-items:center;gap:14px;
            margin-bottom:10px;cursor:pointer;transition:all .2s;
            border:2px solid transparent;
        }
        .pe-card:hover{transform:translateX(3px);}
        .pe-card.selected{border-color:currentColor;}

        .pe-card.libre{background:#f0fdf4;}
        .pe-card.libre .pe-ic{background:#dcfce7;color:#16a34a;}
        .pe-card.libre .pe-num{color:#16a34a;}
        .pe-card.libre .pe-lbl{color:#15803d;}

        .pe-card.reservado{background:#fefce8;}
        .pe-card.reservado .pe-ic{background:#fef9c3;color:#b45309;}
        .pe-card.reservado .pe-num{color:#b45309;}
        .pe-card.reservado .pe-lbl{color:#92400e;}

        .pe-card.financiamiento{background:#eff6ff;}
        .pe-card.financiamiento .pe-ic{background:#dbeafe;color:#1d4ed8;}
        .pe-card.financiamiento .pe-num{color:#1d4ed8;}
        .pe-card.financiamiento .pe-lbl{color:#1e40af;}

        .pe-card.vendido{background:#fff1f2;}
        .pe-card.vendido .pe-ic{background:#ffe4e6;color:#b91c1c;}
        .pe-card.vendido .pe-num{color:#b91c1c;}
        .pe-card.vendido .pe-lbl{color:#991b1b;}

        .pe-ic{
            width:42px;height:42px;border-radius:12px;
            display:flex;align-items:center;justify-content:center;
            font-size:18px;flex-shrink:0;
        }
        .pe-info{}
        .pe-num{font-size:26px;font-weight:900;line-height:1;}
        .pe-lbl{font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;}

        .pe-total{
            margin:6px 16px 16px;
            background:linear-gradient(135deg,rgba(238,0,187,.07),rgba(85,51,204,.07));
            border:1px solid rgba(238,0,187,.15);
            border-radius:14px;padding:14px 16px;
            display:flex;align-items:center;justify-content:space-between;
        }
        .pe-total-lbl{font-size:13px;font-weight:700;color:var(--text);}
        .pe-total-num{font-size:22px;font-weight:900;color:var(--mg);}

        /* ===== PANEL DERECHO — ADMINISTRACIÓN DE LOTES ===== */
        .panel-admin{
            background:var(--white);border-radius:18px;
            border:1px solid var(--border);
            box-shadow:0 2px 12px rgba(0,0,0,.05);
            overflow:hidden;
        }
        .pa-head{
            padding:18px 22px 14px;
            border-bottom:1px solid var(--border);
            display:flex;align-items:center;justify-content:space-between;
            flex-wrap:wrap;gap:12px;
        }
        .pa-title{font-size:14px;font-weight:700;color:var(--text);}
        .pa-title span{color:var(--vt);}

        /* buscador + filtros */
        .pa-toolbar{
            padding:14px 20px;
            border-bottom:1px solid var(--border);
            display:flex;align-items:center;gap:12px;
            flex-wrap:wrap;
        }
        .search-wrap{
            flex:1;min-width:180px;
            display:flex;align-items:center;gap:9px;
            background:var(--bg);border:1.5px solid var(--border);
            border-radius:12px;padding:9px 14px;transition:.2s;
        }
        .search-wrap:focus-within{border-color:rgba(85,51,204,.4);background:#fff;}
        .search-wrap i{color:var(--gray);font-size:13px;}
        .search-wrap input{
            background:none;border:none;outline:none;
            font-family:'Poppins',sans-serif;font-size:13px;
            color:var(--text);width:100%;
        }
        .search-wrap input::placeholder{color:var(--gray);}

        .filtros{display:flex;align-items:center;gap:6px;flex-wrap:wrap;}
        .filtro-btn{
            padding:7px 14px;border-radius:50px;
            font-family:'Poppins',sans-serif;font-size:12px;font-weight:600;
            border:1.5px solid var(--border);
            background:#fff;color:var(--gray);
            cursor:pointer;transition:all .2s;
        }
        .filtro-btn:hover{border-color:var(--vt);color:var(--vt);}
        .filtro-btn.active{background:var(--vt);border-color:var(--vt);color:#fff;}
        .filtro-btn.active-libre{background:#16a34a;border-color:#16a34a;color:#fff;}
        .filtro-btn.active-reservado{background:#b45309;border-color:#b45309;color:#fff;}
        .filtro-btn.active-financiamiento{background:#1d4ed8;border-color:#1d4ed8;color:#fff;}
        .filtro-btn.active-vendido{background:#b91c1c;border-color:#b91c1c;color:#fff;}

        /* tabla */
        .lotes-table{width:100%;overflow-x:auto;}
        .lt-head{
            display:grid;
            grid-template-columns:80px 70px 110px 160px 150px 1fr;
            padding:11px 20px;
            background:var(--bg);border-bottom:1px solid var(--border);
            font-size:10.5px;font-weight:700;color:var(--gray);
            letter-spacing:.8px;text-transform:uppercase;
        }
        .lt-body{}
        .lt-row{
            display:grid;
            grid-template-columns:80px 70px 110px 160px 150px 1fr;
            padding:14px 20px;align-items:center;
            border-bottom:1px solid var(--border);
            transition:.15s;
        }
        .lt-row:last-child{border-bottom:none;}
        .lt-row:hover{background:#fafaff;}
        .lt-row.oculto{display:none;}

        .lt-manzana{
            display:inline-flex;align-items:center;justify-content:center;
            width:36px;height:36px;border-radius:10px;
            background:linear-gradient(135deg,var(--vt),var(--vt2));
            color:#fff;font-size:14px;font-weight:800;
        }
        .lt-num{font-size:14px;font-weight:700;color:var(--text);}
        .lt-metraje{font-size:13px;color:var(--text);}
        .lt-precio{font-size:13.5px;font-weight:700;color:var(--mg);}

        /* badges de estado en tabla */
        .est-badge{
            display:inline-flex;align-items:center;gap:5px;
            font-size:11px;font-weight:600;padding:4px 11px;border-radius:50px;
        }
        .est-libre{background:#dcfce7;color:#16a34a;}
        .est-reservado{background:#fef9c3;color:#b45309;}
        .est-financiamiento{background:#dbeafe;color:#1d4ed8;}
        .est-vendido{background:#ffe4e6;color:#b91c1c;}

        /* botones de acción */
        .acciones{display:flex;align-items:center;gap:5px;flex-wrap:wrap;}
        .ac-btn{
            display:inline-flex;align-items:center;gap:4px;
            padding:5px 10px;border-radius:8px;
            font-family:'Poppins',sans-serif;font-size:11px;font-weight:600;
            border:none;cursor:pointer;transition:all .2s;
            text-decoration:none;
        }
        .ac-ver{background:rgba(85,51,204,.1);color:var(--vt);}
        .ac-ver:hover{background:rgba(85,51,204,.2);}
        .ac-libre{background:#dcfce7;color:#16a34a;}
        .ac-libre:hover{background:#bbf7d0;}
        .ac-reservar{background:#fef9c3;color:#b45309;}
        .ac-reservar:hover{background:#fef08a;}
        .ac-financiar{background:#dbeafe;color:#1d4ed8;}
        .ac-financiar:hover{background:#bfdbfe;}
        .ac-vender{background:#ffe4e6;color:#b91c1c;}
        .ac-vender:hover{background:#fecdd3;}

        /* sin resultados */
        .no-resultados{
            text-align:center;padding:48px 20px;color:var(--gray);
            font-size:14px;display:none;
        }
        .no-resultados i{font-size:36px;display:block;margin-bottom:12px;opacity:.4;}

        /* toast notificación */
        .toast{
            position:fixed;bottom:28px;right:28px;z-index:9999;
            background:var(--dark);color:#fff;
            padding:14px 22px;border-radius:14px;
            font-size:13px;font-weight:500;
            box-shadow:0 8px 32px rgba(0,0,0,.25);
            display:flex;align-items:center;gap:10px;
            transform:translateY(100px);opacity:0;
            transition:all .35s ease;
            max-width:320px;
        }
        .toast.show{transform:translateY(0);opacity:1;}
        .toast.toast-ok{border-left:4px solid var(--gn);}
        .toast.toast-err{border-left:4px solid var(--re);}
        .toast i{font-size:16px;}
        .toast-ok i{color:var(--gn);}
        .toast-err i{color:var(--re);}

        /* spinner overlay */
        .spinner-overlay{
            position:fixed;inset:0;z-index:8000;
            background:rgba(26,26,46,.35);backdrop-filter:blur(2px);
            display:flex;align-items:center;justify-content:center;
            opacity:0;pointer-events:none;transition:opacity .2s;
        }
        .spinner-overlay.show{opacity:1;pointer-events:all;}
        .spinner{
            width:48px;height:48px;border-radius:50%;
            border:4px solid rgba(255,255,255,.2);
            border-top-color:var(--mg);
            animation:spin .7s linear infinite;
        }
        @keyframes spin{to{transform:rotate(360deg);}}

        /* responsive */
        @media(max-width:1100px){
            .main-grid{grid-template-columns:260px 1fr;}
        }
        @media(max-width:900px){
            .main-grid{grid-template-columns:1fr;}
            .panel-estado{position:static;}
            .lt-head,.lt-row{grid-template-columns:70px 60px 110px 140px 1fr;}
            .lt-head>*:nth-child(4),.lt-row>*:nth-child(4){display:none;}
        }
        @media(max-width:640px){
            .page-wrap{padding:16px;}
            .nav-links{display:none;}
            .lt-head,.lt-row{grid-template-columns:60px 60px 110px 1fr;}
            .lt-head>*:nth-child(3),.lt-row>*:nth-child(3){display:none;}
        }
    </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
    <a href="{{ url('/admin') }}" class="nav-brand">
        <div class="nav-logo">
            <img src="{{ asset('imagenes/inmobiliaria_bc.jpeg') }}" alt="BC Logo">
        </div>
        <div class="nav-brand-text">
            <span class="nb-name">BC Inmobiliaria</span>
            <span class="nb-sub">Panel Admin</span>
        </div>
    </a>

    <div class="nav-links">
        <a href="{{ url('/admin') }}" class="nav-link">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        <a href="#" class="nav-link active">
            <i class="fas fa-map"></i> Lotes
        </a>
        <a href="{{ url('/admin/proyectos/' . $proyecto->id . '/clientes') }}" class="nav-link">
            <i class="fas fa-users"></i> Clientes
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-hand-holding-usd"></i> Cobranza
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-chart-pie"></i> Ingreso
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-receipt"></i> Egreso
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-cash-register"></i> Caja
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-file-alt"></i> Documentos
        </a>
    </div>

    <div class="nav-right">
        <a href="{{ url('/admin') }}" class="nav-back">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</nav>

<!-- ===== PAGE ===== -->
<div class="page-wrap">

    <!-- Header del proyecto -->
    <div class="page-header">
        <div class="ph-left">
            <div class="ph-breadcrumb">
                <a href="{{ url('/admin') }}">Dashboard</a>
                <span class="sep"><i class="fas fa-chevron-right"></i></span>
                <a href="{{ url('/admin') }}">Proyectos</a>
                <span class="sep"><i class="fas fa-chevron-right"></i></span>
                <span class="current">{{ $proyecto->nombre }}</span>
            </div>
            <h1 class="ph-title"><em>{{ $proyecto->nombre }}</em></h1>
            <p class="ph-subtitle"><i class="fas fa-map-marker-alt" style="color:var(--mg);margin-right:5px;"></i>{{ $proyecto->ubicacion }}</p>
        </div>
        <div class="ph-right">
            <div class="ph-badge">
                <span class="dot"></span>
                {{ ucfirst($proyecto->estado) }}
            </div>
            <div class="ph-badge" style="color:var(--mg);">
                <i class="fas fa-tag" style="font-size:13px;"></i>
                S/. {{ number_format($proyecto->precio_base, 0, '.', ',') }}
            </div>
        </div>
    </div>

    <!-- Grid principal -->
    <div class="main-grid">

        <!-- === Panel Izquierdo: Estado de Lotes === -->
        <div class="panel-estado">
            <div class="pe-head">
                <div class="pe-title">Estado de <span>Lotes</span></div>
                <div class="pe-subtitle">Resumen por categoría</div>
            </div>
            <div class="pe-cards">
                <div class="pe-card libre" data-filtro="libre" onclick="filtrarPorCard('libre', this)">
                    <div class="pe-ic"><i class="fas fa-check-circle"></i></div>
                    <div class="pe-info">
                        <div class="pe-num" id="cnt-libre">{{ $estadisticas['libre'] }}</div>
                        <div class="pe-lbl">Libres</div>
                    </div>
                </div>
                <div class="pe-card reservado" data-filtro="reservado" onclick="filtrarPorCard('reservado', this)">
                    <div class="pe-ic"><i class="fas fa-clock"></i></div>
                    <div class="pe-info">
                        <div class="pe-num" id="cnt-reservado">{{ $estadisticas['reservado'] }}</div>
                        <div class="pe-lbl">Reservados</div>
                    </div>
                </div>
                <div class="pe-card financiamiento" data-filtro="financiamiento" onclick="filtrarPorCard('financiamiento', this)">
                    <div class="pe-ic"><i class="fas fa-credit-card"></i></div>
                    <div class="pe-info">
                        <div class="pe-num" id="cnt-financiamiento">{{ $estadisticas['financiamiento'] }}</div>
                        <div class="pe-lbl">Financiamiento</div>
                    </div>
                </div>
                <div class="pe-card vendido" data-filtro="vendido" onclick="filtrarPorCard('vendido', this)">
                    <div class="pe-ic"><i class="fas fa-home"></i></div>
                    <div class="pe-info">
                        <div class="pe-num" id="cnt-vendido">{{ $estadisticas['vendido'] }}</div>
                        <div class="pe-lbl">Vendidos</div>
                    </div>
                </div>
            </div>
            <div class="pe-total">
                <span class="pe-total-lbl"><i class="fas fa-layer-group" style="margin-right:7px;color:var(--mg);"></i>Total de Lotes</span>
                <span class="pe-total-num" id="cnt-total">{{ $estadisticas['total'] }}</span>
            </div>
        </div>

        <!-- === Panel Derecho: Administración de Lotes === -->
        <div class="panel-admin">
            <div class="pa-head">
                <div class="pa-title">Administración de <span>Lotes</span></div>
            </div>

            <!-- Toolbar -->
            <div class="pa-toolbar">
                <div class="search-wrap">
                    <i class="fas fa-search"></i>
                    <input type="text" id="buscarInput" placeholder="Buscar por manzana o número...">
                </div>
                <div class="filtros">
                    <button class="filtro-btn active" data-filtro="todos" onclick="setFiltro('todos', this)">Todos</button>
                    <button class="filtro-btn" data-filtro="libre" onclick="setFiltro('libre', this)">Libres</button>
                    <button class="filtro-btn" data-filtro="reservado" onclick="setFiltro('reservado', this)">Reservados</button>
                    <button class="filtro-btn" data-filtro="financiamiento" onclick="setFiltro('financiamiento', this)">Financiamiento</button>
                    <button class="filtro-btn" data-filtro="vendido" onclick="setFiltro('vendido', this)">Vendidos</button>
                </div>
            </div>

            <!-- Tabla -->
            <div class="lotes-table">
                <div class="lt-head">
                    <div>Manzana</div>
                    <div>Lote</div>
                    <div>Metraje (m²)</div>
                    <div>Precio Inicial (S/)</div>
                    <div>Estado</div>
                    <div>Acciones</div>
                </div>
                <div class="lt-body" id="lotesBody">
                    @foreach($lotes as $lote)
                    <div class="lt-row"
                         data-id="{{ $lote->id }}"
                         data-estado="{{ $lote->estado }}"
                         data-manzana="{{ strtolower($lote->manzana) }}"
                         data-numero="{{ $lote->numero }}">
                        <div><span class="lt-manzana">{{ $lote->manzana }}</span></div>
                        <div class="lt-num">#{{ $lote->numero }}</div>
                        <div class="lt-metraje">{{ number_format($lote->metraje, 0) }} m²</div>
                        <div class="lt-precio">S/. {{ number_format($lote->precio_inicial, 0, '.', ',') }}</div>
                        <div>
                            <span class="est-badge est-{{ $lote->estado }}" id="badge-{{ $lote->id }}">
                                <i class="fas fa-circle" style="font-size:6px;"></i>
                                {{ ucfirst($lote->estado) }}
                            </span>
                        </div>
                        <div class="acciones">
                            <button class="ac-btn ac-ver" title="Ver detalle" onclick="verLote({{ $lote->id }})">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($lote->estado !== 'libre')
                            <button class="ac-btn ac-libre" title="Marcar como Libre" onclick="cambiarEstado({{ $lote->id }}, 'libre')">
                                <i class="fas fa-check"></i> Libre
                            </button>
                            @endif
                            @if($lote->estado !== 'reservado')
                            <button class="ac-btn ac-reservar" title="Reservar" onclick="cambiarEstado({{ $lote->id }}, 'reservado')">
                                <i class="fas fa-clock"></i> Reservar
                            </button>
                            @endif
                            @if($lote->estado !== 'financiamiento')
                            <button class="ac-btn ac-financiar" title="Financiamiento" onclick="cambiarEstado({{ $lote->id }}, 'financiamiento')">
                                <i class="fas fa-credit-card"></i> Financiar
                            </button>
                            @endif
                            @if($lote->estado !== 'vendido')
                            <button class="ac-btn ac-vender" title="Marcar como Vendido" onclick="cambiarEstado({{ $lote->id }}, 'vendido')">
                                <i class="fas fa-home"></i> Vender
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="no-resultados" id="noResultados">
                    <i class="fas fa-search-minus"></i>
                    No se encontraron lotes con los filtros aplicados.
                </div>
            </div>
        </div>

    </div><!-- /main-grid -->
</div><!-- /page-wrap -->

<!-- Spinner overlay -->
<div class="spinner-overlay" id="spinnerOverlay">
    <div class="spinner"></div>
</div>

<!-- Toast -->
<div class="toast" id="toast">
    <i class="fas fa-check-circle"></i>
    <span id="toastMsg">Estado actualizado correctamente.</span>
</div>

<script>
    const CSRF    = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const BASE    = '{{ url("/") }}';
    let filtroActual = 'todos';
    let busquedaActual = '';

    // ---- CAMBIAR ESTADO AJAX ----
    async function cambiarEstado(loteId, nuevoEstado) {
        mostrarSpinner(true);
        try {
            const resp = await fetch(`${BASE}/admin/lotes/${loteId}/estado`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ estado: nuevoEstado }),
            });

            if (!resp.ok) throw new Error('Error en el servidor');
            const data = await resp.json();

            if (data.success) {
                // Actualizar badge en la fila
                actualizarFila(loteId, nuevoEstado);
                // Actualizar contadores del panel izquierdo
                actualizarContadores(data.estadisticas);
                mostrarToast('Estado actualizado: ' + labelEstado(nuevoEstado), 'ok');
                // Re-aplicar filtro
                aplicarFiltros();
            }
        } catch (e) {
            mostrarToast('Error al actualizar el estado. Intenta de nuevo.', 'err');
        } finally {
            mostrarSpinner(false);
        }
    }

    function actualizarFila(loteId, nuevoEstado) {
        const fila = document.querySelector(`.lt-row[data-id="${loteId}"]`);
        if (!fila) return;

        fila.dataset.estado = nuevoEstado;

        // Actualizar badge
        const badge = document.getElementById(`badge-${loteId}`);
        if (badge) {
            badge.className = `est-badge est-${nuevoEstado}`;
            badge.innerHTML = `<i class="fas fa-circle" style="font-size:6px;"></i> ${labelEstado(nuevoEstado)}`;
        }

        // Actualizar botones de acción
        const acciones = fila.querySelector('.acciones');
        if (acciones) {
            const estados = ['libre', 'reservado', 'financiamiento', 'vendido'];
            const clases  = ['ac-libre', 'ac-reservar', 'ac-financiar', 'ac-vender'];
            const iconos  = ['fas fa-check', 'fas fa-clock', 'fas fa-credit-card', 'fas fa-home'];
            const labels  = ['Libre', 'Reservar', 'Financiar', 'Vender'];

            // Quitar todos excepto "Ver"
            acciones.querySelectorAll('.ac-btn:not(.ac-ver)').forEach(b => b.remove());

            estados.forEach((est, i) => {
                if (est !== nuevoEstado) {
                    const btn = document.createElement('button');
                    btn.className = `ac-btn ${clases[i]}`;
                    btn.title = labels[i];
                    btn.innerHTML = `<i class="${iconos[i]}"></i> ${labels[i]}`;
                    btn.onclick = () => cambiarEstado(loteId, est);
                    acciones.appendChild(btn);
                }
            });
        }
    }

    function actualizarContadores(est) {
        document.getElementById('cnt-libre').textContent          = est.libre;
        document.getElementById('cnt-reservado').textContent      = est.reservado;
        document.getElementById('cnt-financiamiento').textContent = est.financiamiento;
        document.getElementById('cnt-vendido').textContent        = est.vendido;
        document.getElementById('cnt-total').textContent          = est.total;
    }

    function labelEstado(estado) {
        const map = { libre:'Libre', reservado:'Reservado', financiamiento:'Financiamiento', vendido:'Vendido' };
        return map[estado] || estado;
    }

    // ---- FILTROS ----
    function setFiltro(filtro, btn) {
        filtroActual = filtro;
        document.querySelectorAll('.filtro-btn').forEach(b => {
            b.className = 'filtro-btn';
        });
        if (filtro === 'todos') {
            btn.classList.add('active');
        } else {
            btn.classList.add(`active-${filtro}`);
        }
        // Sincronizar cards del panel izquierdo
        document.querySelectorAll('.pe-card').forEach(c => c.classList.remove('selected'));
        if (filtro !== 'todos') {
            const card = document.querySelector(`.pe-card[data-filtro="${filtro}"]`);
            if (card) card.classList.add('selected');
        }
        aplicarFiltros();
    }

    function filtrarPorCard(filtro, card) {
        // Si ya está activo, quitar filtro
        if (card.classList.contains('selected')) {
            card.classList.remove('selected');
            filtroActual = 'todos';
            document.querySelectorAll('.filtro-btn').forEach(b => b.className = 'filtro-btn');
            document.querySelector('.filtro-btn[data-filtro="todos"]').classList.add('active');
        } else {
            document.querySelectorAll('.pe-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            filtroActual = filtro;
            // Sincronizar botones de filtro
            document.querySelectorAll('.filtro-btn').forEach(b => b.className = 'filtro-btn');
            const fbtn = document.querySelector(`.filtro-btn[data-filtro="${filtro}"]`);
            if (fbtn) fbtn.classList.add(`active-${filtro}`);
        }
        aplicarFiltros();
    }

    function aplicarFiltros() {
        const buscar = busquedaActual.toLowerCase().trim();
        const filas  = document.querySelectorAll('.lt-row');
        let visibles = 0;

        filas.forEach(fila => {
            const estadoFila   = fila.dataset.estado;
            const manzanaFila  = fila.dataset.manzana;
            const numeroFila   = fila.dataset.numero;

            const pasaFiltro   = (filtroActual === 'todos') || (estadoFila === filtroActual);
            const pasaBusqueda = !buscar ||
                manzanaFila.includes(buscar) ||
                numeroFila.includes(buscar);

            if (pasaFiltro && pasaBusqueda) {
                fila.classList.remove('oculto');
                visibles++;
            } else {
                fila.classList.add('oculto');
            }
        });

        const noRes = document.getElementById('noResultados');
        noRes.style.display = visibles === 0 ? 'block' : 'none';
    }

    // Buscador
    document.getElementById('buscarInput').addEventListener('input', function() {
        busquedaActual = this.value;
        aplicarFiltros();
    });

    // ---- VER LOTE ----
    function verLote(loteId) {
        mostrarToast('Detalle del lote #' + loteId + ' — próximamente.', 'ok');
    }

    // ---- HELPERS ----
    function mostrarSpinner(show) {
        document.getElementById('spinnerOverlay').classList.toggle('show', show);
    }

    let toastTimer = null;
    function mostrarToast(msg, tipo) {
        const el = document.getElementById('toast');
        const ic = el.querySelector('i');
        document.getElementById('toastMsg').textContent = msg;
        el.className = `toast toast-${tipo}`;
        ic.className = tipo === 'ok' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        void el.offsetWidth; // reflow
        el.classList.add('show');
        if (toastTimer) clearTimeout(toastTimer);
        toastTimer = setTimeout(() => el.classList.remove('show'), 3500);
    }
</script>
</body>
</html>
