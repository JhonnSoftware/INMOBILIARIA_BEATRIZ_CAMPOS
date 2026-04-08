<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $proyecto->nombre . ' | BC Inmobiliaria')</title>
    <link rel="icon" type="image/png" href="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
        :root{--mg:#EE00BB;--mg2:#C4009A;--vt:#5533CC;--vt2:#3D1F99;--bg:#f0f2ff;--dark:#1a1a2e;--dark2:#16213e;--white:#fff;--border:#e8eaf6;--text:#1a1a2e;--gray:#64748b;--green:#10b981;--green-soft:#ecfdf5;--yellow:#d97706;--blue:#2563eb;--red:#dc2626;}
        html,body{min-height:100%;font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}
        .navbar{position:sticky;top:0;z-index:200;background:var(--dark);padding:0 28px;height:66px;display:flex;align-items:center;gap:20px;box-shadow:0 2px 16px rgba(0,0,0,.24);}
        .nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;flex-shrink:0;}
        .nav-logo{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,var(--mg),var(--vt));display:flex;align-items:center;justify-content:center;box-shadow:0 6px 18px rgba(238,0,187,.35);overflow:hidden;}
        .nav-logo img{width:100%;height:100%;object-fit:contain;padding:3px;}
        .nav-brand-text{display:flex;flex-direction:column;line-height:1.05;}
        .nav-brand-text strong{font-size:13px;color:#fff;font-weight:800;}
        .nav-brand-text span{font-size:9px;color:var(--mg);font-weight:700;letter-spacing:2px;text-transform:uppercase;}
        .nav-links{display:flex;align-items:center;gap:4px;flex:1;overflow:auto hidden;}
        .nav-link{display:inline-flex;align-items:center;gap:8px;color:rgba(255,255,255,.62);text-decoration:none;padding:9px 14px;border-radius:12px;font-size:12.5px;font-weight:600;transition:.2s;white-space:nowrap;}
        .nav-link:hover{background:rgba(255,255,255,.08);color:#fff;}
        .nav-link.active{background:linear-gradient(135deg,rgba(238,0,187,.2),rgba(85,51,204,.22));color:#fff;}
        .nav-link.disabled{opacity:.55;cursor:not-allowed;}
        .nav-right{margin-left:auto;}
        .nav-back{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:12px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.07);color:#fff;text-decoration:none;font-size:12px;font-weight:700;transition:.2s;}
        .nav-back:hover{background:rgba(255,255,255,.12);}
        .page-wrap{max-width:1440px;margin:0 auto;padding:28px 32px 40px;}
        .flash{display:flex;align-items:center;gap:10px;padding:14px 18px;border-radius:16px;margin-bottom:18px;font-size:13px;font-weight:600;border:1px solid transparent;}
        .flash.success{background:var(--green-soft);color:#047857;border-color:#a7f3d0;}
        .flash.error{background:#fff1f2;color:#be123c;border-color:#fecdd3;}
        .page-header{background:linear-gradient(135deg,var(--dark2) 0%,var(--dark) 42%,#2d1b69 70%,var(--vt2) 100%);border-radius:24px;padding:28px 32px;margin-bottom:24px;display:flex;align-items:flex-start;justify-content:space-between;gap:18px;position:relative;overflow:hidden;}
        .page-header::before{content:'';position:absolute;right:-50px;top:-50px;width:240px;height:240px;border-radius:50%;background:radial-gradient(circle,rgba(238,0,187,.18) 0%,transparent 70%);}
        .page-header > *{position:relative;z-index:1;}
        .breadcrumbs{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:8px;}
        .breadcrumbs a{font-size:12px;color:rgba(255,255,255,.58);text-decoration:none;}
        .breadcrumbs a:hover{color:#fff;}
        .breadcrumbs span{font-size:12px;color:rgba(255,255,255,.78);}
        .page-title{font-size:28px;font-weight:800;color:#fff;line-height:1.15;}
        .page-title em{font-style:normal;color:var(--mg);}
        .page-subtitle{margin-top:6px;font-size:13px;color:rgba(255,255,255,.62);max-width:760px;line-height:1.65;}
        .header-badges{display:flex;flex-wrap:wrap;gap:10px;justify-content:flex-end;}
        .header-badge{display:inline-flex;align-items:center;gap:8px;padding:8px 16px;border-radius:999px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);color:#fff;font-size:12px;font-weight:700;}
        .header-badge .dot{width:8px;height:8px;border-radius:50%;background:var(--green);}
        .card{background:var(--white);border:1px solid var(--border);border-radius:20px;box-shadow:0 10px 30px rgba(15,23,42,.05);}
        .summary-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:16px;margin-bottom:22px;}
        .summary-card{padding:20px 18px;display:flex;align-items:center;gap:14px;}
        .summary-icon{width:50px;height:50px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
        .summary-card h3{font-size:28px;font-weight:900;line-height:1;}
        .summary-card p{font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;margin-top:4px;}
        .summary-card.is-libre .summary-icon{background:#dcfce7;color:#16a34a;}.summary-card.is-libre h3,.summary-card.is-libre p{color:#15803d;}
        .summary-card.is-reservado .summary-icon{background:#fef3c7;color:#d97706;}.summary-card.is-reservado h3,.summary-card.is-reservado p{color:#b45309;}
        .summary-card.is-financiamiento .summary-icon{background:#dbeafe;color:#2563eb;}.summary-card.is-financiamiento h3,.summary-card.is-financiamiento p{color:#1d4ed8;}
        .summary-card.is-vendido .summary-icon{background:#fee2e2;color:#dc2626;}.summary-card.is-vendido h3,.summary-card.is-vendido p{color:#b91c1c;}
        .summary-card.is-total .summary-icon{background:#f3e8ff;color:var(--vt);}.summary-card.is-total h3,.summary-card.is-total p{color:var(--vt);}
        .content-card{padding:22px;}
        .section-head{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:18px;}
        .section-title{font-size:17px;font-weight:800;color:var(--text);}
        .section-title span{color:var(--mg);}
        .toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:18px;}
        .search-form{display:flex;align-items:center;gap:12px;flex-wrap:wrap;flex:1;}
        .search-box{min-width:260px;flex:1;display:flex;align-items:center;gap:10px;background:var(--bg);border:1.5px solid var(--border);border-radius:14px;padding:12px 14px;}
        .search-box i{color:var(--gray);}
        .search-box input{width:100%;border:none;outline:none;background:transparent;font:500 13px 'Poppins',sans-serif;color:var(--text);}
        .filter-group{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
        .filter-pill{display:inline-flex;align-items:center;gap:6px;padding:10px 14px;border-radius:999px;background:#fff;border:1.5px solid var(--border);text-decoration:none;font-size:12px;font-weight:700;color:var(--gray);transition:.2s;}
        .filter-pill:hover{border-color:rgba(85,51,204,.35);color:var(--vt);}
        .filter-pill.active{background:var(--vt);border-color:var(--vt);color:#fff;}
        .btn-primary,.btn-secondary{display:inline-flex;align-items:center;gap:8px;border-radius:14px;padding:11px 16px;text-decoration:none;cursor:pointer;transition:.2s;font:700 13px 'Poppins',sans-serif;}
        .btn-primary{background:linear-gradient(135deg,var(--mg),var(--vt));color:#fff;border:none;box-shadow:0 12px 26px rgba(85,51,204,.22);}
        .btn-primary:hover{transform:translateY(-1px);}
        .btn-secondary{background:#fff;color:var(--gray);border:1.5px solid var(--border);}
        .btn-secondary:hover{border-color:rgba(85,51,204,.35);color:var(--vt);}
        .table-wrap{overflow-x:auto;border-radius:16px;border:1px solid var(--border);}
        table{width:100%;border-collapse:collapse;background:#fff;}
        thead th{background:var(--bg);padding:14px 16px;text-align:left;border-bottom:1px solid var(--border);font-size:11px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);}
        tbody td{padding:16px;border-bottom:1px solid var(--border);font-size:13px;color:var(--text);vertical-align:middle;}
        tbody tr:last-child td{border-bottom:none;}
        tbody tr:hover{background:#fafaff;}
        .cell-strong{font-weight:700;}
        .muted{color:var(--gray);}
        .state-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;}
        .state-badge::before{content:'';width:8px;height:8px;border-radius:50%;}
        .state-Libre{background:#dcfce7;color:#15803d;}.state-Libre::before{background:#16a34a;}
        .state-Reservado{background:#fef3c7;color:#b45309;}.state-Reservado::before{background:#d97706;}
        .state-Financiamiento{background:#dbeafe;color:#1d4ed8;}.state-Financiamiento::before{background:#2563eb;}
        .state-Vendido{background:#fee2e2;color:#b91c1c;}.state-Vendido::before{background:#dc2626;}
        .empty-state{padding:48px 20px;text-align:center;color:var(--gray);}
        .empty-state i{font-size:38px;display:block;margin-bottom:12px;opacity:.5;}
        .pagination{margin-top:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}
        .pagination-status{font-size:12px;color:var(--gray);font-weight:600;}
        .pagination-links{display:flex;gap:8px;}
        .page-link{display:inline-flex;align-items:center;gap:6px;padding:10px 14px;border-radius:12px;border:1.5px solid var(--border);background:#fff;color:var(--gray);text-decoration:none;font-size:12px;font-weight:700;}
        .page-link:hover{border-color:rgba(85,51,204,.35);color:var(--vt);}
        .page-link.disabled{pointer-events:none;opacity:.5;}
        .form-card{padding:24px;}
        .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;}
        .form-group label{display:block;margin-bottom:8px;font-size:12px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;color:var(--text);}
        .form-group span.req{color:var(--mg);}
        .form-group input,.form-group select,.form-group textarea{width:100%;border:1.5px solid var(--border);background:var(--bg);border-radius:14px;padding:13px 14px;outline:none;font:500 13px 'Poppins',sans-serif;color:var(--text);transition:.2s;}
        .form-group textarea{min-height:120px;resize:vertical;}
        .form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:rgba(238,0,187,.35);background:#fff;box-shadow:0 0 0 4px rgba(238,0,187,.08);}
        .form-group.full{grid-column:1 / -1;}
        .error-text{margin-top:8px;font-size:12px;color:#be123c;font-weight:600;}
        .form-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:22px;flex-wrap:wrap;}
        @media(max-width:1100px){.summary-grid{grid-template-columns:repeat(3,minmax(0,1fr));}}
        @media(max-width:860px){.page-wrap{padding:20px 18px 32px;}.page-header{padding:22px;}.summary-grid{grid-template-columns:repeat(2,minmax(0,1fr));}.form-grid{grid-template-columns:1fr;}}
        @media(max-width:640px){.navbar{padding:0 16px;}.nav-brand-text{display:none;}.page-header{padding:18px;}.page-title{font-size:22px;}.summary-grid{grid-template-columns:1fr;}.search-box{min-width:100%;}}
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar">
    <a href="{{ route('admin.dashboard') }}" class="nav-brand">
        <div class="nav-logo"><img src="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}" alt="BC"></div>
        <div class="nav-brand-text"><strong>BC Inmobiliaria</strong><span>Panel Admin</span></div>
    </a>
    <div class="nav-links">
        <a href="{{ route('admin.proyectos.show', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'dashboard' ? 'active' : '' }}"><i class="fas fa-table-cells-large"></i> Dashboard</a>
        <a href="{{ route('admin.proyectos.lotes', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'lotes' ? 'active' : '' }}"><i class="fas fa-map"></i> Lotes</a>
        <a href="{{ route('admin.proyectos.clientes', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'clientes' ? 'active' : '' }}"><i class="fas fa-users"></i> Clientes</a>
        <a href="{{ route('admin.proyectos.cobranza', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'cobranza' ? 'active' : '' }}"><i class="fas fa-hand-holding-usd"></i> Cobranza</a>
        <a href="{{ route('admin.proyectos.ingresos', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'ingresos' ? 'active' : '' }}"><i class="fas fa-chart-pie"></i> Ingresos</a>
        <a href="{{ route('admin.proyectos.egresos', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'egresos' ? 'active' : '' }}"><i class="fas fa-receipt"></i> Egresos</a>
        <span class="nav-link disabled"><i class="fas fa-cash-register"></i> Caja</span>
        <span class="nav-link disabled"><i class="fas fa-file-alt"></i> Documentos</span>
    </div>
    <div class="nav-right"><a href="{{ route('admin.dashboard') }}" class="nav-back"><i class="fas fa-arrow-left"></i> Volver</a></div>
</nav>

<div class="page-wrap">
    @if(session('success'))
    <div class="flash success"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
    @endif
    @if($errors->any())
    <div class="flash error"><i class="fas fa-exclamation-circle"></i><span>{{ $errors->first() }}</span></div>
    @endif

    <header class="page-header">
        <div>
            <div class="breadcrumbs">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <span><i class="fas fa-chevron-right"></i></span>
                <a href="{{ route('admin.proyectos.lotes', $proyecto) }}">{{ $proyecto->nombre }}</a>
                <span><i class="fas fa-chevron-right"></i></span>
                <span>@yield('module_label', 'Módulo')</span>
            </div>
            <h1 class="page-title">{!! $__env->yieldContent('page_title') !!}</h1>
            <p class="page-subtitle">@yield('page_subtitle')</p>
        </div>
        <div class="header-badges">
            <span class="header-badge"><span class="dot"></span>{{ ucfirst($proyecto->estado) }}</span>
            <span class="header-badge"><i class="fas fa-location-dot"></i>{{ $proyecto->direccion ?: $proyecto->ubicacion ?: 'Ubicación por definir' }}</span>
        </div>
    </header>

    @yield('content')
</div>

@stack('scripts')
</body>
</html>
