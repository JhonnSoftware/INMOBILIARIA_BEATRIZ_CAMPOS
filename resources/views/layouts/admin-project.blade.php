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
        :root{
            --mg:#EE00BB;--mg2:#C4009A;--vt:#5533CC;--vt2:#3D1F99;
            --bg:#f5f6fa;--white:#fff;
            --border:#e4e6ef;--border2:#ececf5;
            --text:#1e1b3a;--gray:#64748b;--gray2:#94a3b8;
            --green:#10b981;--green-soft:#ecfdf5;--yellow:#d97706;--blue:#2563eb;--red:#dc2626;
        }
        html,body{min-height:100%;font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}

        /* ══ NAVBAR (claro) ══ */
        .navbar{position:sticky;top:0;z-index:200;background:#fff;border-bottom:1.5px solid var(--border);padding:0 24px;height:62px;display:flex;align-items:center;gap:16px;box-shadow:0 2px 12px rgba(85,51,204,.05);}
        .nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;flex-shrink:0;}
        .nav-logo{width:38px;height:38px;border-radius:11px;background:linear-gradient(135deg,#fff0fa,#f0ebff);border:1.5px solid #e8d8f8;display:flex;align-items:center;justify-content:center;overflow:hidden;}
        .nav-logo img{width:100%;height:100%;object-fit:contain;padding:3px;}
        .nav-brand-text{display:flex;flex-direction:column;line-height:1.05;}
        .nav-brand-text strong{font-size:12.5px;color:var(--text);font-weight:800;}
        .nav-brand-text span{font-size:9px;color:var(--mg);font-weight:700;letter-spacing:2px;text-transform:uppercase;}

        .nav-divider{width:1px;height:32px;background:var(--border);flex-shrink:0;margin:0 4px;}
        .nav-links{display:flex;align-items:center;gap:2px;flex:1;overflow:auto hidden;}
        .nav-link{display:inline-flex;align-items:center;gap:7px;color:var(--gray);text-decoration:none;padding:8px 12px;border-radius:11px;font-size:12.5px;font-weight:600;transition:.18s;white-space:nowrap;}
        .nav-link:hover{background:#f3f0ff;color:var(--vt);}
        .nav-link.active{background:linear-gradient(135deg,rgba(238,0,187,.09),rgba(85,51,204,.11));color:var(--vt);font-weight:700;}
        .nav-link.disabled{opacity:.4;cursor:not-allowed;pointer-events:none;}
        .nav-right{margin-left:auto;flex-shrink:0;}
        .nav-back{display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border-radius:11px;border:1.5px solid var(--border);background:#fff;color:var(--gray);text-decoration:none;font-size:12px;font-weight:700;transition:.2s;}
        .nav-back:hover{border-color:rgba(85,51,204,.3);color:var(--vt);background:#faf8ff;}

        /* ══ CONTENT ══ */
        .page-wrap{max-width:1440px;margin:0 auto;padding:26px 28px 40px;}
        .flash{display:flex;align-items:center;gap:10px;padding:13px 18px;border-radius:14px;margin-bottom:18px;font-size:13px;font-weight:600;border:1px solid transparent;}
        .flash.success{background:var(--green-soft);color:#047857;border-color:#a7f3d0;}
        .flash.error{background:#fff1f2;color:#be123c;border-color:#fecdd3;}

        /* ══ PAGE HEADER (claro) ══ */
        .page-header{
            background:#fff;border:1.5px solid var(--border);
            border-radius:20px;padding:22px 28px;margin-bottom:22px;
            display:flex;align-items:flex-start;justify-content:space-between;gap:18px;
            position:relative;overflow:hidden;
            box-shadow:0 4px 18px rgba(85,51,204,.05);
        }
        .page-header::before{content:'';position:absolute;right:-40px;top:-40px;width:180px;height:180px;border-radius:50%;background:radial-gradient(circle,rgba(238,0,187,.07) 0%,transparent 70%);}
        .page-header::after{content:'';position:absolute;left:-30px;bottom:-40px;width:140px;height:140px;border-radius:50%;background:radial-gradient(circle,rgba(85,51,204,.05) 0%,transparent 70%);}
        .page-header>*{position:relative;z-index:1;}
        .breadcrumbs{display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin-bottom:8px;}
        .breadcrumbs a{font-size:12px;color:var(--gray2);text-decoration:none;font-weight:600;}
        .breadcrumbs a:hover{color:var(--vt);}
        .breadcrumbs span{font-size:12px;color:var(--gray2);}
        .page-title{font-size:24px;font-weight:900;color:var(--text);line-height:1.2;}
        .page-title em{font-style:normal;color:var(--mg);}
        .page-subtitle{margin-top:5px;font-size:12.5px;color:var(--gray);max-width:700px;line-height:1.65;}
        .header-badges{display:flex;flex-wrap:wrap;gap:8px;justify-content:flex-end;align-items:flex-start;}
        .header-badge{display:inline-flex;align-items:center;gap:7px;padding:7px 14px;border-radius:999px;background:var(--bg);border:1.5px solid var(--border);color:var(--gray);font-size:12px;font-weight:700;}
        .header-badge .dot{width:8px;height:8px;border-radius:50%;background:var(--green);}

        /* ══ CARDS ══ */
        .card{background:#fff;border:1.5px solid var(--border);border-radius:18px;box-shadow:0 2px 12px rgba(85,51,204,.04);}
        .summary-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin-bottom:22px;}
        .summary-card{padding:18px 16px;display:flex;align-items:center;gap:12px;}
        .summary-icon{width:46px;height:46px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
        .summary-card h3{font-size:26px;font-weight:900;line-height:1;}
        .summary-card p{font-size:10.5px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;margin-top:4px;}
        .summary-card.is-libre .summary-icon{background:#dcfce7;color:#16a34a;}.summary-card.is-libre h3,.summary-card.is-libre p{color:#15803d;}
        .summary-card.is-reservado .summary-icon{background:#fef3c7;color:#d97706;}.summary-card.is-reservado h3,.summary-card.is-reservado p{color:#b45309;}
        .summary-card.is-financiamiento .summary-icon{background:#dbeafe;color:#2563eb;}.summary-card.is-financiamiento h3,.summary-card.is-financiamiento p{color:#1d4ed8;}
        .summary-card.is-vendido .summary-icon{background:#fee2e2;color:#dc2626;}.summary-card.is-vendido h3,.summary-card.is-vendido p{color:#b91c1c;}
        .summary-card.is-total .summary-icon{background:#f3e8ff;color:var(--vt);}.summary-card.is-total h3,.summary-card.is-total p{color:var(--vt);}

        /* ══ SECTION ══ */
        .content-card{padding:22px;}
        .section-head{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:18px;}
        .section-title{font-size:16px;font-weight:800;color:var(--text);}
        .section-title span{color:var(--mg);}

        /* ══ TOOLBAR ══ */
        .toolbar{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:18px;}
        .search-form{display:flex;align-items:center;gap:12px;flex-wrap:wrap;flex:1;}
        .search-box{min-width:240px;flex:1;display:flex;align-items:center;gap:10px;background:var(--bg);border:1.5px solid var(--border);border-radius:13px;padding:11px 14px;}
        .search-box i{color:var(--gray2);}
        .search-box input{width:100%;border:none;outline:none;background:transparent;font:500 13px 'Poppins',sans-serif;color:var(--text);}
        .filter-group{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
        .filter-pill{display:inline-flex;align-items:center;gap:6px;padding:9px 14px;border-radius:999px;background:#fff;border:1.5px solid var(--border);text-decoration:none;font-size:12px;font-weight:700;color:var(--gray);transition:.2s;}
        .filter-pill:hover{border-color:rgba(85,51,204,.35);color:var(--vt);}
        .filter-pill.active{background:var(--vt);border-color:var(--vt);color:#fff;}

        /* ══ BUTTONS ══ */
        .btn-primary,.btn-secondary{display:inline-flex;align-items:center;gap:8px;border-radius:13px;padding:11px 16px;text-decoration:none;cursor:pointer;transition:.2s;font:700 13px 'Poppins',sans-serif;}
        .btn-primary{background:linear-gradient(135deg,var(--mg),var(--vt));color:#fff;border:none;box-shadow:0 8px 20px rgba(85,51,204,.18);}
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 12px 26px rgba(85,51,204,.26);}
        .btn-secondary{background:#fff;color:var(--gray);border:1.5px solid var(--border);}
        .btn-secondary:hover{border-color:rgba(85,51,204,.35);color:var(--vt);background:#faf8ff;}

        /* ══ TABLE ══ */
        .table-wrap{overflow-x:auto;border-radius:14px;border:1.5px solid var(--border);}
        table{width:100%;border-collapse:collapse;background:#fff;}
        thead th{background:var(--bg);padding:13px 16px;text-align:left;border-bottom:1.5px solid var(--border);font-size:10.5px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);}
        tbody td{padding:15px 16px;border-bottom:1px solid var(--border2);font-size:13px;color:var(--text);vertical-align:middle;}
        tbody tr:last-child td{border-bottom:none;}
        tbody tr:hover td{background:#fafaff;}
        .cell-strong{font-weight:700;}
        .muted{color:var(--gray);}

        /* ══ BADGES ══ */
        .state-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:700;}
        .state-badge::before{content:'';width:8px;height:8px;border-radius:50%;}
        .state-Libre{background:#dcfce7;color:#15803d;}.state-Libre::before{background:#16a34a;}
        .state-Reservado{background:#fef3c7;color:#b45309;}.state-Reservado::before{background:#d97706;}
        .state-Financiamiento{background:#dbeafe;color:#1d4ed8;}.state-Financiamiento::before{background:#2563eb;}
        .state-Vendido{background:#fee2e2;color:#b91c1c;}.state-Vendido::before{background:#dc2626;}

        /* ══ EMPTY ══ */
        .empty-state{padding:48px 20px;text-align:center;color:var(--gray);}
        .empty-state i{font-size:36px;display:block;margin-bottom:12px;opacity:.4;}

        /* ══ PAGINATION ══ */
        .pagination{margin-top:16px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;padding-top:16px;border-top:1px solid var(--border2);}
        .pagination-status{font-size:12px;color:var(--gray);font-weight:600;}
        .pagination-links{display:flex;gap:8px;}
        .page-link{display:inline-flex;align-items:center;gap:6px;padding:9px 14px;border-radius:10px;border:1.5px solid var(--border);background:#fff;color:var(--gray);text-decoration:none;font-size:12px;font-weight:700;transition:.2s;}
        .page-link:hover:not(.disabled){border-color:var(--vt);color:var(--vt);}
        .page-link.disabled{pointer-events:none;opacity:.45;}

        /* ══ FORM ══ */
        .form-card{padding:24px;}
        .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;}
        .form-group{display:grid;}
        .form-group label{display:block;margin-bottom:8px;font-size:11.5px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--text);}
        .form-group span.req{color:var(--mg);}
        .form-group input,.form-group select,.form-group textarea{width:100%;border:1.5px solid var(--border);background:var(--bg);border-radius:13px;padding:12px 14px;outline:none;font:500 13px 'Poppins',sans-serif;color:var(--text);transition:.2s;}
        .form-group textarea{min-height:120px;resize:vertical;}
        .form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:rgba(238,0,187,.4);background:#fff;box-shadow:0 0 0 4px rgba(238,0,187,.07);}
        .form-group.full{grid-column:1 / -1;}
        .helper-text{margin-top:6px;font-size:11.5px;color:var(--gray2);}
        .error-text{margin-top:7px;font-size:11.5px;color:#be123c;font-weight:600;}
        .form-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:22px;flex-wrap:wrap;}

        /* ══ RESPONSIVE ══ */
        @media(max-width:1100px){.summary-grid{grid-template-columns:repeat(3,minmax(0,1fr));}}
        @media(max-width:860px){.page-wrap{padding:18px 16px 32px;}.page-header{padding:18px 20px;}.summary-grid{grid-template-columns:repeat(2,minmax(0,1fr));}.form-grid{grid-template-columns:1fr;}}
        @media(max-width:640px){.navbar{padding:0 14px;}.nav-brand-text{display:none;}.page-title{font-size:20px;}.summary-grid{grid-template-columns:1fr;}.search-box{min-width:100%;}}
    </style>
    @stack('styles')
</head>
<body>
<nav class="navbar">
    <a href="{{ route('admin.dashboard') }}" class="nav-brand">
        <div class="nav-logo"><img src="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}" alt="BC"></div>
        <div class="nav-brand-text"><strong>BC Inmobiliaria</strong><span>Panel Admin</span></div>
    </a>
    <div class="nav-divider"></div>
    <div class="nav-links">
        <a href="{{ route('admin.proyectos.dashboard', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'dashboard' ? 'active' : '' }}"><i class="fas fa-table-cells-large"></i> Dashboard</a>
        <a href="{{ route('admin.proyectos.lotes', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'lotes' ? 'active' : '' }}"><i class="fas fa-map"></i> Lotes</a>
        <a href="{{ route('admin.proyectos.clientes', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'clientes' ? 'active' : '' }}"><i class="fas fa-users"></i> Clientes</a>
        <a href="{{ route('admin.proyectos.cobranza', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'cobranza' ? 'active' : '' }}"><i class="fas fa-hand-holding-usd"></i> Cobranza</a>
        <a href="{{ route('admin.proyectos.ingresos', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'ingresos' ? 'active' : '' }}"><i class="fas fa-chart-pie"></i> Ingresos</a>
        <a href="{{ route('admin.proyectos.egresos', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'egresos' ? 'active' : '' }}"><i class="fas fa-receipt"></i> Egresos</a>
        <a href="{{ route('admin.proyectos.caja', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'caja' ? 'active' : '' }}"><i class="fas fa-cash-register"></i> Caja</a>
        <a href="{{ route('admin.proyectos.documentos', $proyecto) }}" class="nav-link {{ ($currentModule ?? '') === 'documentos' ? 'active' : '' }}"><i class="fas fa-file-alt"></i> Documentos</a>
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
                <a href="{{ route('admin.proyectos.dashboard', $proyecto) }}">{{ $proyecto->nombre }}</a>
                <span><i class="fas fa-chevron-right"></i></span>
                <span>@yield('module_label', 'Módulo')</span>
            </div>
            <h1 class="page-title">@yield('page_title')</h1>
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
