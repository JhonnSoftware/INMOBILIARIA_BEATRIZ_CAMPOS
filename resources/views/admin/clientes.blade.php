<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Clientes — {{ $proyecto->nombre }}</title>
    <link rel="icon" type="image/png" href="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
        :root{
            --mg:#EE00BB; --mg2:#C4009A;
            --vt:#5533CC; --vt2:#3D1F99;
            --dark:#1a1a2e; --dark2:#16213e;
            --bg:#f0f2ff; --white:#ffffff;
            --border:#e8eaf6; --gray:#64748b; --text:#1a1a2e;
            --gn:#10b981; --gn2:#059669;
            --yw:#f59e0b; --yw2:#d97706;
            --bl:#3b82f6; --bl2:#1d4ed8;
            --re:#ef4444; --re2:#dc2626;
            --te:#0d9488; --te2:#0f766e;
            --navbar-h:64px;
        }
        html,body{height:100%;font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}

        /* ===== NAVBAR ===== */
        .navbar{height:var(--navbar-h);background:var(--dark);display:flex;align-items:center;padding:0 28px;position:sticky;top:0;z-index:200;box-shadow:0 2px 16px rgba(0,0,0,.25);gap:0;}
        .nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;margin-right:32px;flex-shrink:0;}
        .nav-logo{width:40px;height:40px;border-radius:11px;background:linear-gradient(135deg,var(--mg),var(--vt));display:flex;align-items:center;justify-content:center;overflow:hidden;box-shadow:0 4px 14px rgba(238,0,187,.35);}
        .nav-logo img{width:100%;height:100%;object-fit:contain;padding:3px;}
        .nav-brand-text{display:flex;flex-direction:column;line-height:1.1;}
        .nav-brand-text .nb-name{font-size:13px;font-weight:800;color:#fff;}
        .nav-brand-text .nb-sub{font-size:9px;font-weight:600;color:var(--mg);letter-spacing:2px;text-transform:uppercase;}
        .nav-links{display:flex;align-items:center;gap:4px;flex:1;}
        .nav-link{display:flex;align-items:center;gap:7px;padding:8px 14px;border-radius:10px;text-decoration:none;color:rgba(255,255,255,.6);font-size:12.5px;font-weight:500;transition:all .2s;white-space:nowrap;}
        .nav-link:hover{color:#fff;background:rgba(255,255,255,.07);}
        .nav-link.active{color:#fff;background:linear-gradient(135deg,rgba(238,0,187,.2),rgba(85,51,204,.2));}
        .nav-link i{font-size:13px;}
        .nav-right{display:flex;align-items:center;gap:10px;margin-left:auto;flex-shrink:0;}
        .nav-back{display:flex;align-items:center;gap:7px;padding:8px 16px;border-radius:10px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.7);text-decoration:none;font-size:12px;font-weight:600;transition:.2s;}
        .nav-back:hover{background:rgba(255,255,255,.12);color:#fff;}

        /* ===== PAGE WRAP ===== */
        .page-wrap{padding:28px 32px;max-width:1500px;margin:0 auto;}

        /* ===== PAGE HEADER ===== */
        .page-header{background:linear-gradient(135deg,var(--dark2) 0%,var(--dark) 40%,#2d1b69 70%,var(--vt2) 100%);border-radius:20px;padding:26px 32px;display:flex;align-items:center;justify-content:space-between;margin-bottom:26px;position:relative;overflow:hidden;}
        .page-header::before{content:'';position:absolute;right:-40px;top:-40px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(238,0,187,.18) 0%,transparent 70%);}
        .ph-breadcrumb{display:flex;align-items:center;gap:8px;margin-bottom:6px;}
        .ph-breadcrumb a{font-size:12px;color:rgba(255,255,255,.5);text-decoration:none;transition:.2s;}
        .ph-breadcrumb a:hover{color:rgba(255,255,255,.8);}
        .ph-breadcrumb .sep{color:rgba(255,255,255,.2);font-size:11px;}
        .ph-breadcrumb .current{font-size:12px;color:rgba(255,255,255,.8);}
        .ph-title{font-size:22px;font-weight:800;color:#fff;margin-bottom:4px;}
        .ph-title em{font-style:normal;color:var(--mg);}
        .ph-subtitle{font-size:13px;color:rgba(255,255,255,.55);}
        .ph-right{display:flex;align-items:center;gap:10px;position:relative;z-index:1;}
        .btn-nuevo{display:flex;align-items:center;gap:8px;padding:10px 20px;border-radius:12px;background:linear-gradient(135deg,var(--mg),var(--vt));color:#fff;font-weight:700;font-size:13px;border:none;cursor:pointer;box-shadow:0 4px 16px rgba(238,0,187,.35);transition:.2s;}
        .btn-nuevo:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(238,0,187,.45);}

        /* ===== STATS STRIP ===== */
        .stats-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px;}
        .stat-card{background:var(--white);border-radius:16px;padding:16px 20px;border:1px solid var(--border);display:flex;align-items:center;gap:14px;box-shadow:0 2px 8px rgba(0,0,0,.04);}
        .stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
        .stat-icon.gn{background:rgba(16,185,129,.12);color:var(--gn);}
        .stat-icon.yw{background:rgba(245,158,11,.12);color:var(--yw);}
        .stat-icon.bl{background:rgba(59,130,246,.12);color:var(--bl);}
        .stat-icon.re{background:rgba(239,68,68,.12);color:var(--re);}
        .stat-icon.mg{background:rgba(238,0,187,.12);color:var(--mg);}
        .stat-val{font-size:22px;font-weight:800;line-height:1;}
        .stat-lbl{font-size:11px;color:var(--gray);font-weight:500;text-transform:uppercase;letter-spacing:.5px;margin-top:2px;}

        /* ===== MAIN PANEL ===== */
        .main-panel{background:var(--white);border-radius:18px;border:1px solid var(--border);box-shadow:0 2px 12px rgba(0,0,0,.05);overflow:hidden;}
        .panel-top{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px;flex-wrap:wrap;}
        .panel-title{font-size:16px;font-weight:700;color:var(--text);}
        .panel-title span{color:var(--mg);}
        .search-box{display:flex;align-items:center;gap:10px;background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:8px 14px;flex:1;min-width:200px;max-width:380px;margin-left:auto;}
        .search-box i{color:var(--gray);font-size:13px;}
        .search-box input{border:none;background:transparent;font-size:13px;font-family:'Poppins',sans-serif;color:var(--text);outline:none;width:100%;}
        .filter-btns{display:flex;gap:6px;flex-wrap:wrap;}
        .fb{padding:6px 14px;border-radius:10px;font-size:12px;font-weight:600;border:1px solid var(--border);background:transparent;cursor:pointer;transition:.2s;color:var(--gray);}
        .fb:hover{border-color:var(--vt);color:var(--vt);}
        .fb.active{background:var(--vt);color:#fff;border-color:var(--vt);}

        /* ===== TABLE ===== */
        .table-wrap{overflow-x:auto;}
        table{width:100%;border-collapse:collapse;}
        thead th{padding:12px 16px;text-align:left;font-size:11px;font-weight:700;color:var(--gray);text-transform:uppercase;letter-spacing:.5px;border-bottom:2px solid var(--border);white-space:nowrap;}
        tbody tr{border-bottom:1px solid var(--border);transition:.15s;}
        tbody tr:hover{background:#f8f9ff;}
        tbody tr:last-child{border-bottom:none;}
        td{padding:13px 16px;font-size:13px;vertical-align:middle;}
        .client-name{font-weight:700;color:var(--text);font-size:13.5px;}
        .client-phone{font-size:12px;color:var(--gray);margin-top:2px;}
        .client-phone i{font-size:11px;margin-right:3px;}
        .td-ubicacion{font-size:12.5px;}
        .td-ubicacion .mz-badge{display:inline-block;padding:2px 8px;border-radius:6px;background:linear-gradient(135deg,var(--vt),var(--mg));color:#fff;font-weight:700;font-size:11px;margin-right:4px;}
        .td-precio .precio-total{font-weight:700;color:var(--text);}
        .td-precio .precio-cuota{font-size:11px;color:var(--gray);}
        .estado-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;}
        .estado-badge.reservado{background:rgba(245,158,11,.12);color:#92400e;}
        .estado-badge.financiamiento{background:rgba(59,130,246,.12);color:var(--bl2);}
        .estado-badge.vendido{background:rgba(16,185,129,.12);color:var(--gn2);}
        .estado-badge.desistido{background:rgba(239,68,68,.12);color:var(--re2);}
        .estado-badge::before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor;}
        .actions{display:flex;gap:6px;align-items:center;}
        .act-btn{width:32px;height:32px;border-radius:8px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;transition:.2s;}
        .act-btn:hover{transform:translateY(-1px);}
        .act-edit{background:rgba(100,116,139,.1);color:var(--gray);}
        .act-edit:hover{background:rgba(100,116,139,.2);}
        .act-desistido{background:rgba(245,158,11,.1);color:var(--yw2);}
        .act-desistido:hover{background:rgba(245,158,11,.25);}
        .act-docs{background:rgba(59,130,246,.1);color:var(--bl);}
        .act-docs:hover{background:rgba(59,130,246,.25);}
        .act-coment{background:rgba(13,148,136,.1);color:var(--te);}
        .act-coment:hover{background:rgba(13,148,136,.25);}
        .act-del{background:rgba(239,68,68,.1);color:var(--re);}
        .act-del:hover{background:rgba(239,68,68,.25);}
        .empty-row td{text-align:center;padding:40px;color:var(--gray);}
        .empty-row i{font-size:32px;display:block;margin-bottom:10px;opacity:.3;}

        /* ===== MODAL BASE ===== */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:500;display:none;align-items:center;justify-content:center;padding:20px;}
        .modal-overlay.open{display:flex;}
        .modal{background:#fff;border-radius:20px;width:100%;max-width:720px;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.25);}
        .modal-sm{max-width:500px;}
        .modal-lg{max-width:860px;}
        .modal-head{padding:22px 28px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;}
        .modal-head h3{font-size:17px;font-weight:800;color:var(--text);}
        .modal-head h3 span{color:var(--mg);}
        .modal-close{width:34px;height:34px;border-radius:10px;border:none;background:var(--bg);cursor:pointer;font-size:16px;color:var(--gray);display:flex;align-items:center;justify-content:center;transition:.2s;}
        .modal-close:hover{background:rgba(239,68,68,.1);color:var(--re);}
        .modal-body{padding:28px;overflow-y:auto;flex:1;}
        .modal-foot{padding:18px 28px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;flex-shrink:0;}

        /* ===== FORM GRID ===== */
        .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        .form-full{grid-column:1/-1;}
        .form-group{display:flex;flex-direction:column;gap:6px;}
        .form-group label{font-size:12px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.4px;}
        .form-group label span{color:var(--re);margin-left:2px;}
        .form-control{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:11px;font-size:13px;font-family:'Poppins',sans-serif;color:var(--text);background:#fff;transition:.2s;outline:none;}
        .form-control:focus{border-color:var(--vt);box-shadow:0 0 0 3px rgba(85,51,204,.1);}
        select.form-control{cursor:pointer;}

        /* ===== BTNS ===== */
        .btn{display:inline-flex;align-items:center;gap:7px;padding:10px 22px;border-radius:11px;font-size:13px;font-weight:700;border:none;cursor:pointer;transition:.2s;font-family:'Poppins',sans-serif;}
        .btn-primary{background:linear-gradient(135deg,var(--mg),var(--vt));color:#fff;box-shadow:0 4px 14px rgba(238,0,187,.3);}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(238,0,187,.4);}
        .btn-secondary{background:var(--bg);color:var(--gray);border:1px solid var(--border);}
        .btn-secondary:hover{background:var(--border);}
        .btn-danger{background:linear-gradient(135deg,var(--re),var(--re2));color:#fff;}
        .btn-danger:hover{transform:translateY(-1px);}
        .btn-teal{background:linear-gradient(135deg,var(--te),var(--te2));color:#fff;}
        .btn-teal:hover{transform:translateY(-1px);}

        /* ===== COMENTARIOS ===== */
        .comentarios-list{display:flex;flex-direction:column;gap:12px;margin-bottom:20px;}
        .coment-item{background:var(--bg);border-radius:12px;padding:14px 16px;border-left:3px solid var(--vt);}
        .coment-meta{display:flex;justify-content:space-between;margin-bottom:6px;}
        .coment-autor{font-size:12px;font-weight:700;color:var(--vt);}
        .coment-fecha{font-size:11px;color:var(--gray);}
        .coment-texto{font-size:13px;color:var(--text);line-height:1.5;}
        .coment-empty{text-align:center;padding:24px;color:var(--gray);font-size:13px;}
        .coment-empty i{font-size:28px;display:block;margin-bottom:8px;opacity:.3;}
        .add-coment{display:flex;flex-direction:column;gap:8px;padding-top:16px;border-top:1px solid var(--border);}
        .add-coment label{font-size:12px;font-weight:700;color:var(--text);}
        .add-coment textarea{resize:vertical;min-height:80px;}

        /* ===== DOCUMENTOS ===== */
        .docs-list{display:flex;flex-direction:column;gap:10px;margin-bottom:18px;}
        .doc-item{display:flex;align-items:center;gap:12px;padding:12px 16px;background:var(--bg);border-radius:12px;}
        .doc-icon{width:38px;height:38px;border-radius:10px;background:linear-gradient(135deg,var(--bl),var(--bl2));display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;flex-shrink:0;}
        .doc-info{flex:1;min-width:0;}
        .doc-name{font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .doc-meta{font-size:11px;color:var(--gray);}
        .doc-actions{display:flex;gap:6px;}
        .doc-btn{width:30px;height:30px;border-radius:8px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;transition:.2s;}
        .doc-btn.download{background:rgba(59,130,246,.1);color:var(--bl);}
        .doc-btn.del{background:rgba(239,68,68,.1);color:var(--re);}
        .doc-empty{text-align:center;padding:24px;color:var(--gray);font-size:13px;}
        .doc-empty i{font-size:28px;display:block;margin-bottom:8px;opacity:.3;}
        .upload-area{border:2px dashed var(--border);border-radius:12px;padding:24px;text-align:center;cursor:pointer;transition:.2s;}
        .upload-area:hover{border-color:var(--vt);background:rgba(85,51,204,.03);}
        .upload-area i{font-size:28px;color:var(--vt);margin-bottom:8px;display:block;}
        .upload-area p{font-size:13px;color:var(--gray);}
        .upload-area input[type=file]{display:none;}

        /* ===== ALERT FLASH ===== */
        .flash{padding:12px 20px;border-radius:12px;font-size:13px;font-weight:600;margin-bottom:18px;display:flex;align-items:center;gap:10px;}
        .flash.success{background:rgba(16,185,129,.1);color:var(--gn2);border:1px solid rgba(16,185,129,.2);}
        .flash.error{background:rgba(239,68,68,.1);color:var(--re2);border:1px solid rgba(239,68,68,.2);}

        /* ===== TOAST ===== */
        .toast{position:fixed;bottom:24px;right:24px;z-index:999;background:#1a1a2e;color:#fff;padding:12px 20px;border-radius:14px;font-size:13px;font-weight:600;box-shadow:0 8px 30px rgba(0,0,0,.25);display:none;align-items:center;gap:10px;min-width:200px;}
        .toast.show{display:flex;}
        .toast.ok i{color:var(--gn);}
        .toast.err i{color:var(--re);}

        /* ===== RESPONSIVE ===== */
        @media(max-width:900px){
            .nav-links{display:none;}
            .page-wrap{padding:16px;}
            .stats-strip{grid-template-columns:1fr 1fr;}
            .form-grid{grid-template-columns:1fr;}
        }
        @media(max-width:600px){
            .stats-strip{grid-template-columns:1fr;}
            .ph-right{display:none;}
        }
    </style>
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
    <a href="{{ route('admin.dashboard') }}" class="nav-brand">
        <div class="nav-logo">
            <img src="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}" alt="BC">
        </div>
        <div class="nav-brand-text">
            <span class="nb-name">BC Inmobiliaria</span>
            <span class="nb-sub">Panel Admin</span>
        </div>
    </a>

    <div class="nav-links">
        <a href="{{ route('admin.dashboard') }}" class="nav-link">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        <a href="{{ route('admin.proyectos.show', $proyecto) }}" class="nav-link">
            <i class="fas fa-map"></i> Lotes
        </a>
        <a href="{{ route('admin.proyectos.clientes', $proyecto) }}" class="nav-link active">
            <i class="fas fa-users"></i> Clientes
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-hand-holding-usd"></i> Cobranza
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-arrow-down"></i> Ingreso
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-arrow-up"></i> Egreso
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-cash-register"></i> Caja
        </a>
        <a href="#" class="nav-link">
            <i class="fas fa-file-alt"></i> Documentos
        </a>
    </div>

    <div class="nav-right">
        <a href="{{ route('admin.proyectos.show', $proyecto) }}" class="nav-back">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</nav>

<!-- ===== PAGE WRAP ===== -->
<div class="page-wrap">

    {{-- Flash message --}}
    @if(session('success'))
    <div class="flash success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="flash error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="ph-left">
            <div class="ph-breadcrumb">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <span class="sep">›</span>
                <a href="{{ route('admin.proyectos.show', $proyecto) }}">{{ $proyecto->nombre }}</a>
                <span class="sep">›</span>
                <span class="current">Clientes</span>
            </div>
            <div class="ph-title">Clientes de <em>{{ $proyecto->nombre }}</em></div>
            <div class="ph-subtitle">Gestiona todos los clientes registrados en este proyecto</div>
        </div>
        <div class="ph-right">
            <button class="btn-nuevo" onclick="abrirModalNuevo()">
                <i class="fas fa-user-plus"></i> Registrar Cliente
            </button>
        </div>
    </div>

    <!-- STATS STRIP -->
    @php
        $total       = $clientes->count();
        $reservados  = $clientes->where('estado','reservado')->count();
        $financiados = $clientes->where('estado','financiamiento')->count();
        $vendidos    = $clientes->where('estado','vendido')->count();
        $desistidos  = $clientes->where('estado','desistido')->count();
    @endphp
    <div class="stats-strip">
        <div class="stat-card">
            <div class="stat-icon mg"><i class="fas fa-users"></i></div>
            <div>
                <div class="stat-val">{{ $total }}</div>
                <div class="stat-lbl">Total Clientes</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon yw"><i class="fas fa-clock"></i></div>
            <div>
                <div class="stat-val">{{ $reservados }}</div>
                <div class="stat-lbl">Reservados</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon bl"><i class="fas fa-credit-card"></i></div>
            <div>
                <div class="stat-val">{{ $financiados }}</div>
                <div class="stat-lbl">Financiamiento</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon re"><i class="fas fa-user-times"></i></div>
            <div>
                <div class="stat-val">{{ $desistidos }}</div>
                <div class="stat-lbl">Desistidos</div>
            </div>
        </div>
    </div>

    <!-- MAIN PANEL -->
    <div class="main-panel">
        <div class="panel-top">
            <div class="panel-title">Administración de <span>Clientes</span></div>
            <div class="filter-btns">
                <button class="fb active" onclick="filtrar('todos',this)">Todos</button>
                <button class="fb" onclick="filtrar('reservado',this)">Reservados</button>
                <button class="fb" onclick="filtrar('financiamiento',this)">Financiamiento</button>
                <button class="fb" onclick="filtrar('vendido',this)">Vendidos</button>
                <button class="fb" onclick="filtrar('desistido',this)">Desistidos</button>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="buscador" placeholder="Buscar cliente, DNI..." oninput="buscar(this.value)">
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>DNI</th>
                        <th>Ubicación</th>
                        <th>Precio</th>
                        <th>Registro</th>
                        <th>Estado</th>
                        <th>Asesor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaBody">
                    @forelse($clientes as $cliente)
                    <tr class="cliente-row"
                        data-estado="{{ $cliente->estado }}"
                        data-search="{{ strtolower($cliente->nombre . ' ' . $cliente->apellido . ' ' . $cliente->dni . ' ' . $cliente->telefono) }}"
                        id="row-{{ $cliente->id }}">
                        <td>
                            <div class="client-name">{{ $cliente->nombre }} {{ $cliente->apellido }}</div>
                            @if($cliente->telefono)
                            <div class="client-phone"><i class="fas fa-phone"></i> {{ $cliente->telefono }}</div>
                            @endif
                        </td>
                        <td>{{ $cliente->dni }}</td>
                        <td class="td-ubicacion">
                            @if($cliente->manzana)
                            <span class="mz-badge">{{ $cliente->manzana }}</span>
                            @endif
                            @if($cliente->numero_lote) Lote: <strong>{{ $cliente->numero_lote }}</strong>@endif
                        </td>
                        <td class="td-precio">
                            @if($cliente->precio_lote)
                            <div class="precio-total">S/ {{ number_format($cliente->precio_lote, 2) }}</div>
                            @endif
                            @if($cliente->cuota_mensual)
                            <div class="precio-cuota">Cuota: S/ {{ number_format($cliente->cuota_mensual, 2) }}</div>
                            @endif
                        </td>
                        <td>{{ $cliente->fecha_registro ? $cliente->fecha_registro->format('d/m/Y') : ($cliente->created_at ? $cliente->created_at->format('d/m/Y') : '—') }}</td>
                        <td>
                            <span class="estado-badge {{ $cliente->estado }}">{{ ucfirst($cliente->estado) }}</span>
                        </td>
                        <td>{{ $cliente->asesor ?? '—' }}</td>
                        <td>
                            <div class="actions">
                                <button class="act-btn act-edit" title="Editar cliente"
                                    onclick="abrirModalEditar({{ $cliente->id }}, {{ json_encode($cliente) }})">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="act-btn act-desistido" title="Marcar como desistido"
                                    onclick="marcarDesistido({{ $cliente->id }}, this)">
                                    <i class="fas fa-user-slash"></i>
                                </button>
                                <button class="act-btn act-docs" title="Subir documentos"
                                    onclick="abrirDocs({{ $cliente->id }}, '{{ $cliente->nombre }} {{ $cliente->apellido }}')">
                                    <i class="fas fa-folder-open"></i>
                                </button>
                                <button class="act-btn act-coment" title="Ver comentarios"
                                    onclick="abrirComentarios({{ $cliente->id }}, '{{ $cliente->nombre }} {{ $cliente->apellido }}')">
                                    <i class="fas fa-comment-dots"></i>
                                </button>
                                <button class="act-btn act-del" title="Eliminar cliente"
                                    onclick="eliminarCliente({{ $cliente->id }}, '{{ $cliente->nombre }} {{ $cliente->apellido }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row">
                        <td colspan="8">
                            <i class="fas fa-users"></i>
                            No hay clientes registrados en este proyecto.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div><!-- /page-wrap -->

<!-- ===== MODAL: REGISTRAR / EDITAR CLIENTE ===== -->
<div class="modal-overlay" id="modalCliente">
    <div class="modal modal-lg">
        <div class="modal-head">
            <h3 id="modalClienteTitulo">Registrar <span>Cliente</span></h3>
            <button class="modal-close" onclick="cerrarModal('modalCliente')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <form id="formCliente" method="POST" action="{{ route('admin.proyectos.clientes.store', $proyecto) }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nombres <span>*</span></label>
                        <input type="text" name="nombre" id="fNombre" class="form-control" placeholder="Ej: Jorge Luis" required>
                    </div>
                    <div class="form-group">
                        <label>Apellidos <span>*</span></label>
                        <input type="text" name="apellido" id="fApellido" class="form-control" placeholder="Ej: Pérez García" required>
                    </div>
                    <div class="form-group">
                        <label>DNI <span>*</span></label>
                        <input type="text" name="dni" id="fDni" class="form-control" placeholder="Ej: 12345678" maxlength="20">
                    </div>
                    <div class="form-group">
                        <label>Manzana</label>
                        <input type="text" name="manzana" id="fManzana" class="form-control" placeholder="Ej: A">
                    </div>
                    <div class="form-group">
                        <label>Lote</label>
                        <input type="text" name="numero_lote" id="fLote" class="form-control" placeholder="Ej: 3">
                    </div>
                    <div class="form-group">
                        <label>Precio del Lote (S/)</label>
                        <input type="number" name="precio_lote" id="fPrecio" class="form-control" placeholder="Ej: 35000" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label>Cuota Mensual (S/)</label>
                        <input type="number" name="cuota_mensual" id="fCuota" class="form-control" placeholder="Ej: 1200" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" id="fTelefono" class="form-control" placeholder="Ej: 987654321">
                    </div>
                    <div class="form-group form-full">
                        <label>Dirección</label>
                        <input type="text" name="direccion" id="fDireccion" class="form-control" placeholder="Ej: Jr. Las Flores 123, Lima">
                    </div>
                    <div class="form-group">
                        <label>Fecha de Registro</label>
                        <input type="date" name="fecha_registro" id="fFecha" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Asesor</label>
                        <input type="text" name="asesor" id="fAsesor" class="form-control" placeholder="Nombre del asesor">
                    </div>
                    <div class="form-group form-full">
                        <label>Estado <span>*</span></label>
                        <select name="estado" id="fEstado" class="form-control" required>
                            <option value="reservado">Reservado</option>
                            <option value="financiamiento">Financiamiento</option>
                            <option value="vendido">Vendido</option>
                            <option value="desistido">Desistido</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-foot">
            <button class="btn btn-secondary" onclick="cerrarModal('modalCliente')">Cancelar</button>
            <button class="btn btn-primary" onclick="submitFormCliente()">
                <i class="fas fa-save"></i> <span id="btnGuardarTxt">Registrar Cliente</span>
            </button>
        </div>
    </div>
</div>

<!-- ===== MODAL: COMENTARIOS ===== -->
<div class="modal-overlay" id="modalComentarios">
    <div class="modal modal-sm">
        <div class="modal-head">
            <h3>Comentarios — <span id="comentClienteNombre"></span></h3>
            <button class="modal-close" onclick="cerrarModal('modalComentarios')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="comentarios-list" id="comentariosList"></div>
            <div class="add-coment">
                <label>Agregar comentario</label>
                <textarea class="form-control add-coment" id="nuevoComentTxt" placeholder="Escribe un comentario..." rows="3"></textarea>
                <button class="btn btn-teal" onclick="guardarComentario()" style="margin-top:4px">
                    <i class="fas fa-paper-plane"></i> Enviar
                </button>
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn btn-secondary" onclick="cerrarModal('modalComentarios')">Cerrar</button>
        </div>
    </div>
</div>

<!-- ===== MODAL: DOCUMENTOS ===== -->
<div class="modal-overlay" id="modalDocumentos">
    <div class="modal">
        <div class="modal-head">
            <h3>Documentos — <span id="docsClienteNombre"></span></h3>
            <button class="modal-close" onclick="cerrarModal('modalDocumentos')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="docs-list" id="docsList"></div>
            <div class="upload-area" onclick="document.getElementById('uploadInput').click()">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Haz clic para subir un documento</p>
                <p style="font-size:11px;margin-top:4px;">PDF, JPG, PNG, Word, Excel — máx. 10MB</p>
                <input type="file" id="uploadInput" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" onchange="subirDocumento(this)">
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn btn-secondary" onclick="cerrarModal('modalDocumentos')">Cerrar</button>
        </div>
    </div>
</div>

<!-- ===== MODAL: CONFIRMAR ELIMINAR ===== -->
<div class="modal-overlay" id="modalEliminar">
    <div class="modal modal-sm">
        <div class="modal-head">
            <h3>Eliminar <span>Cliente</span></h3>
            <button class="modal-close" onclick="cerrarModal('modalEliminar')"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <p style="font-size:14px;color:var(--gray);line-height:1.6;">
                ¿Estás seguro de que deseas eliminar a <strong id="elimNombre"></strong>?<br>
                Esta acción también eliminará sus comentarios y documentos.
            </p>
        </div>
        <div class="modal-foot">
            <button class="btn btn-secondary" onclick="cerrarModal('modalEliminar')">Cancelar</button>
            <button class="btn btn-danger" id="btnConfirmarEliminar"><i class="fas fa-trash"></i> Eliminar</button>
        </div>
    </div>
</div>

<!-- ===== TOAST ===== -->
<div class="toast" id="toast"><i class="fas fa-check-circle"></i> <span id="toastMsg"></span></div>

<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const BASE   = @json(route('admin.proyectos.show', $proyecto));

// ─── UTILS ───────────────────────────────────────────────────────────────────
function abrirModal(id){ document.getElementById(id).classList.add('open'); }
function cerrarModal(id){ document.getElementById(id).classList.remove('open'); }
function showToast(msg, ok=true){
    const t = document.getElementById('toast');
    t.querySelector('span').textContent = msg;
    t.className = 'toast show ' + (ok ? 'ok' : 'err');
    setTimeout(()=>{ t.className='toast'; }, 3000);
}

// ─── FILTRAR / BUSCAR ────────────────────────────────────────────────────────
let filtroActivo = 'todos';
function filtrar(estado, btn){
    filtroActivo = estado;
    document.querySelectorAll('.fb').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    aplicarFiltros();
}
function buscar(q){ aplicarFiltros(q); }
function aplicarFiltros(q = document.getElementById('buscador').value.toLowerCase()){
    document.querySelectorAll('.cliente-row').forEach(row => {
        const okEstado = filtroActivo === 'todos' || row.dataset.estado === filtroActivo;
        const okSearch = !q || row.dataset.search.includes(q);
        row.style.display = (okEstado && okSearch) ? '' : 'none';
    });
}

// ─── MODAL NUEVO / EDITAR ────────────────────────────────────────────────────
let editClienteId = null;

function abrirModalNuevo(){
    editClienteId = null;
    document.getElementById('modalClienteTitulo').innerHTML = 'Registrar <span>Cliente</span>';
    document.getElementById('btnGuardarTxt').textContent = 'Registrar Cliente';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('formCliente').action = BASE + '/clientes';
    document.getElementById('formCliente').reset();
    document.getElementById('fFecha').value = new Date().toISOString().split('T')[0];
    abrirModal('modalCliente');
}

function abrirModalEditar(id, data){
    editClienteId = id;
    document.getElementById('modalClienteTitulo').innerHTML = 'Editar <span>Cliente</span>';
    document.getElementById('btnGuardarTxt').textContent = 'Guardar Cambios';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('formCliente').action = BASE + '/clientes/' + id;
    document.getElementById('fNombre').value    = data.nombre      || '';
    document.getElementById('fApellido').value  = data.apellido    || '';
    document.getElementById('fDni').value       = data.dni         || '';
    document.getElementById('fManzana').value   = data.manzana     || '';
    document.getElementById('fLote').value      = data.numero_lote || '';
    document.getElementById('fPrecio').value    = data.precio_lote || '';
    document.getElementById('fCuota').value     = data.cuota_mensual || '';
    document.getElementById('fTelefono').value  = data.telefono    || '';
    document.getElementById('fDireccion').value = data.direccion   || '';
    document.getElementById('fFecha').value     = data.fecha_registro ? data.fecha_registro.split('T')[0] : '';
    document.getElementById('fAsesor').value    = data.asesor      || '';
    document.getElementById('fEstado').value    = data.estado      || 'reservado';
    abrirModal('modalCliente');
}

function submitFormCliente(){
    document.getElementById('formCliente').submit();
}

// ─── DESISTIDO ────────────────────────────────────────────────────────────────
function marcarDesistido(id, btn){
    if(!confirm('¿Marcar este cliente como desistido?')) return;
    fetch(BASE + '/clientes/' + id + '/desistido', {
        method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json'}
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.ok){
            const row = document.getElementById('row-'+id);
            row.dataset.estado = 'desistido';
            row.querySelector('.estado-badge').className = 'estado-badge desistido';
            row.querySelector('.estado-badge').textContent = 'Desistido';
            showToast('Cliente marcado como desistido.');
            aplicarFiltros();
        }
    });
}

// ─── ELIMINAR ─────────────────────────────────────────────────────────────────
let eliminarId = null;
function eliminarCliente(id, nombre){
    eliminarId = id;
    document.getElementById('elimNombre').textContent = nombre;
    abrirModal('modalEliminar');
}
document.getElementById('btnConfirmarEliminar').addEventListener('click', ()=>{
    if(!eliminarId) return;
    fetch(BASE + '/clientes/' + eliminarId, {
        method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF}
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.ok){
            document.getElementById('row-'+eliminarId).remove();
            cerrarModal('modalEliminar');
            showToast('Cliente eliminado.');
        }
    });
});

// ─── COMENTARIOS ──────────────────────────────────────────────────────────────
let comentClienteId = null;
function abrirComentarios(id, nombre){
    comentClienteId = id;
    document.getElementById('comentClienteNombre').textContent = nombre;
    document.getElementById('nuevoComentTxt').value = '';
    document.getElementById('comentariosList').innerHTML = '<p style="text-align:center;color:var(--gray);font-size:13px;">Cargando...</p>';
    abrirModal('modalComentarios');
    fetch(BASE + '/clientes/' + id + '/comentarios', {headers:{'X-CSRF-TOKEN':CSRF}})
        .then(r=>r.json())
        .then(lista => renderComentarios(lista));
}
function renderComentarios(lista){
    const el = document.getElementById('comentariosList');
    if(!lista.length){
        el.innerHTML = '<div class="coment-empty"><i class="fas fa-comment-slash"></i>Sin comentarios aún.</div>';
        return;
    }
    el.innerHTML = lista.map(c=>`
        <div class="coment-item">
            <div class="coment-meta">
                <span class="coment-autor"><i class="fas fa-user"></i> ${c.autor || 'Admin'}</span>
                <span class="coment-fecha">${c.fecha}</span>
            </div>
            <div class="coment-texto">${c.texto}</div>
        </div>
    `).join('');
}
function guardarComentario(){
    const txt = document.getElementById('nuevoComentTxt').value.trim();
    if(!txt) return;
    fetch(BASE + '/clientes/' + comentClienteId + '/comentarios', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json'},
        body: JSON.stringify({texto:txt, autor:'Admin'})
    })
    .then(r=>r.json())
    .then(d=>{
        if(d.ok){
            document.getElementById('nuevoComentTxt').value = '';
            // Recargar lista
            fetch(BASE + '/clientes/' + comentClienteId + '/comentarios')
                .then(r=>r.json()).then(lista=>renderComentarios(lista));
            showToast('Comentario guardado.');
        }
    });
}

// ─── DOCUMENTOS ───────────────────────────────────────────────────────────────
let docsClienteId = null;
function abrirDocs(id, nombre){
    docsClienteId = id;
    document.getElementById('docsClienteNombre').textContent = nombre;
    document.getElementById('docsList').innerHTML = '<p style="text-align:center;color:var(--gray);font-size:13px;">Cargando...</p>';
    abrirModal('modalDocumentos');
    cargarDocs();
}
function cargarDocs(){
    fetch(BASE + '/clientes/' + docsClienteId + '/documentos')
        .then(r=>r.json())
        .then(lista => renderDocs(lista));
}
function renderDocs(lista){
    const el = document.getElementById('docsList');
    if(!lista.length){
        el.innerHTML = '<div class="doc-empty"><i class="fas fa-folder-open"></i>Sin documentos subidos.</div>';
        return;
    }
    el.innerHTML = lista.map(d=>`
        <div class="doc-item" id="doc-${d.id}">
            <div class="doc-icon"><i class="fas fa-file-alt"></i></div>
            <div class="doc-info">
                <div class="doc-name">${d.nombre}</div>
                <div class="doc-meta">${d.fecha} · ${d.tamanio ? Math.round(d.tamanio/1024)+' KB' : ''}</div>
            </div>
            <div class="doc-actions">
                <a href="${d.url}" target="_blank" class="doc-btn download" title="Descargar"><i class="fas fa-download"></i></a>
                <button class="doc-btn del" onclick="eliminarDoc(${d.id})" title="Eliminar"><i class="fas fa-trash"></i></button>
            </div>
        </div>
    `).join('');
}
function subirDocumento(input){
    if(!input.files[0]) return;
    const fd = new FormData();
    fd.append('archivo', input.files[0]);
    fd.append('_token', CSRF);
    fetch(BASE + '/clientes/' + docsClienteId + '/documentos', {method:'POST', body:fd})
        .then(r=>r.json())
        .then(d=>{
            if(d.ok){ cargarDocs(); showToast('Documento subido.'); }
            else showToast('Error al subir.', false);
        });
    input.value = '';
}
function eliminarDoc(docId){
    if(!confirm('¿Eliminar este documento?')) return;
    fetch(BASE + '/clientes/' + docsClienteId + '/documentos/' + docId, {
        method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF}
    })
    .then(r=>r.json())
    .then(d=>{ if(d.ok){ document.getElementById('doc-'+docId).remove(); showToast('Documento eliminado.'); }});
}

// ─── CERRAR MODAL CLICK FUERA ─────────────────────────────────────────────────
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => {
        if(e.target === overlay) overlay.classList.remove('open');
    });
});
</script>
</body>
</html>
