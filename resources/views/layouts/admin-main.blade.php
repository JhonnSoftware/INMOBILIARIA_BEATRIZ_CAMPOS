<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Administrativo | BC Inmobiliaria')</title>
    <link rel="icon" type="image/png" href="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
        :root{--mg:#EE00BB;--vt:#5533CC;--vt2:#3D1F99;--bg:#f0f2ff;--dark:#1a1a2e;--dark2:#16213e;--white:#fff;--border:#e8eaf6;--text:#1a1a2e;--gray:#64748b;--green:#10b981;--red:#dc2626;--yellow:#d97706;--blue:#2563eb;--sidebar-w:270px;}
        html,body{min-height:100%;font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}
        body{display:flex;min-height:100vh;}
        .sidebar{width:var(--sidebar-w);background:var(--dark);color:#fff;padding:24px 16px;display:flex;flex-direction:column;gap:18px;position:fixed;top:0;bottom:0;left:0;overflow:auto;}
        .sb-brand{display:flex;align-items:center;gap:12px;padding:0 8px 12px;border-bottom:1px solid rgba(255,255,255,.06);}
        .sb-logo{width:46px;height:46px;border-radius:14px;background:#fff;display:flex;align-items:center;justify-content:center;overflow:hidden;box-shadow:0 8px 20px rgba(238,0,187,.28);}
        .sb-logo img{width:100%;height:100%;object-fit:contain;padding:4px;}
        .sb-brand strong{display:block;font-size:14px;font-weight:800;line-height:1.2;}
        .sb-brand span{display:block;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--mg);margin-top:2px;}
        .sb-section-title{padding:0 8px;font-size:10px;font-weight:800;letter-spacing:1.7px;text-transform:uppercase;color:rgba(255,255,255,.3);}
        .sb-links{display:grid;gap:6px;}
        .sb-link{display:flex;align-items:center;gap:12px;padding:11px 12px;border-radius:14px;text-decoration:none;color:rgba(255,255,255,.66);font-size:13px;font-weight:600;transition:.2s;}
        .sb-link:hover{background:rgba(255,255,255,.06);color:#fff;}
        .sb-link.active{background:linear-gradient(135deg,rgba(238,0,187,.2),rgba(85,51,204,.22));color:#fff;}
        .sb-link.disabled{opacity:.45;cursor:not-allowed;pointer-events:none;}
        .sb-icon{width:34px;height:34px;border-radius:11px;background:rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .sb-link.active .sb-icon{background:linear-gradient(135deg,var(--mg),var(--vt));}
        .sb-project-link{padding:10px 12px;}
        .sb-project-meta{display:flex;flex-direction:column;min-width:0;flex:1;}
        .sb-project-meta strong{font-size:12.5px;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .sb-project-meta span{font-size:10.5px;color:rgba(255,255,255,.42);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;}
        .sb-project-badge{margin-left:auto;display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:22px;padding:0 8px;border-radius:999px;background:rgba(238,0,187,.16);border:1px solid rgba(238,0,187,.18);font-size:10px;font-weight:800;color:#ff9be8;flex-shrink:0;}
        .sb-project-empty{padding:10px 12px;border-radius:14px;background:rgba(255,255,255,.04);border:1px dashed rgba(255,255,255,.08);font-size:12px;line-height:1.6;color:rgba(255,255,255,.45);}
        .sb-footer{margin-top:auto;border-top:1px solid rgba(255,255,255,.06);padding-top:16px;display:grid;gap:10px;}
        .sb-user{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:14px;background:rgba(255,255,255,.06);}
        .sb-avatar{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--mg),var(--vt));display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0;}
        .sb-user strong{display:block;font-size:13px;line-height:1.3;}
        .sb-user span{display:block;font-size:11px;color:rgba(255,255,255,.45);}
        .sb-logout-btn{width:100%;display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:10px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.05);color:#fff;font:700 12px 'Poppins',sans-serif;cursor:pointer;transition:.2s;}
        .sb-logout-btn:hover{background:linear-gradient(135deg,var(--mg),var(--vt));border-color:transparent;}
        .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;}
        .topbar{position:sticky;top:0;z-index:40;height:80px;background:#fff;border-bottom:2px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 28px;box-shadow:0 4px 18px rgba(15,23,42,.05);}
        .topbar-title{font-size:20px;font-weight:800;color:var(--text);}
        .topbar-title span{color:var(--mg);}
        .topbar-right{display:flex;align-items:center;gap:10px;}
        .topbar-chip{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:12px;background:var(--bg);border:1px solid var(--border);font-size:12px;font-weight:700;color:var(--gray);}
        .topbar-chip i{color:var(--mg);}
        .topbar-user{display:flex;align-items:center;gap:10px;padding:7px 14px 7px 7px;border-radius:14px;background:var(--bg);border:1px solid var(--border);}
        .content{padding:28px;flex:1;}
        .flash{display:flex;align-items:center;gap:10px;padding:14px 18px;border-radius:16px;margin-bottom:18px;font-size:13px;font-weight:600;border:1px solid transparent;}
        .flash.success{background:#ecfdf5;color:#047857;border-color:#a7f3d0;}
        .flash.error{background:#fff1f2;color:#be123c;border-color:#fecdd3;}
        .page-header{background:linear-gradient(135deg,var(--dark2) 0%,var(--dark) 42%,#2d1b69 70%,var(--vt2) 100%);border-radius:24px;padding:28px 32px;margin-bottom:24px;display:flex;align-items:flex-start;justify-content:space-between;gap:18px;position:relative;overflow:hidden;}
        .page-header::before{content:'';position:absolute;right:-50px;top:-50px;width:240px;height:240px;border-radius:50%;background:radial-gradient(circle,rgba(238,0,187,.18) 0%,transparent 70%);}
        .page-header>*{position:relative;z-index:1;}
        .breadcrumbs{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:8px;}
        .breadcrumbs a{font-size:12px;color:rgba(255,255,255,.58);text-decoration:none;}
        .breadcrumbs span{font-size:12px;color:rgba(255,255,255,.78);}
        .page-title{font-size:28px;font-weight:800;color:#fff;line-height:1.15;}
        .page-title em{font-style:normal;color:var(--mg);}
        .page-subtitle{margin-top:6px;font-size:13px;color:rgba(255,255,255,.65);max-width:760px;line-height:1.65;}
        .page-actions{display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end;}
        .btn-primary,.btn-secondary{display:inline-flex;align-items:center;gap:8px;border-radius:14px;padding:11px 16px;text-decoration:none;cursor:pointer;transition:.2s;font:700 13px 'Poppins',sans-serif;}
        .btn-primary{background:linear-gradient(135deg,var(--mg),var(--vt));color:#fff;border:none;box-shadow:0 12px 26px rgba(85,51,204,.22);}
        .btn-primary:hover{transform:translateY(-1px);}
        .btn-secondary{background:#fff;color:var(--gray);border:1.5px solid var(--border);}
        .btn-secondary:hover{border-color:rgba(85,51,204,.35);color:var(--vt);}
        .card{background:#fff;border:1px solid var(--border);border-radius:20px;box-shadow:0 10px 30px rgba(15,23,42,.05);}
        .summary-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:16px;margin-bottom:22px;}
        .summary-card{padding:20px 18px;display:flex;align-items:center;gap:14px;}
        .summary-icon{width:50px;height:50px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;}
        .summary-card h3{font-size:28px;font-weight:900;line-height:1;}
        .summary-card p{font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;margin-top:4px;}
        .summary-card.is-total .summary-icon{background:#f3e8ff;color:var(--vt);}.summary-card.is-total h3,.summary-card.is-total p{color:var(--vt);}
        .summary-card.is-green .summary-icon{background:#dcfce7;color:#16a34a;}.summary-card.is-green h3,.summary-card.is-green p{color:#15803d;}
        .summary-card.is-yellow .summary-icon{background:#fef3c7;color:#d97706;}.summary-card.is-yellow h3,.summary-card.is-yellow p{color:#b45309;}
        .summary-card.is-red .summary-icon{background:#fee2e2;color:#dc2626;}.summary-card.is-red h3,.summary-card.is-red p{color:#b91c1c;}
        .summary-card.is-blue .summary-icon{background:#dbeafe;color:#2563eb;}.summary-card.is-blue h3,.summary-card.is-blue p{color:#1d4ed8;}
        .content-card{padding:22px;}
        .section-head{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:18px;}
        .section-title{font-size:17px;font-weight:800;color:var(--text);}
        .section-title span{color:var(--mg);}
        .toolbar-form{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:18px;}
        .toolbar-select{width:100%;border:1.5px solid var(--border);background:#fff;border-radius:14px;padding:12px 14px;font:600 13px 'Poppins',sans-serif;color:var(--text);}
        .toolbar-actions{display:flex;gap:10px;flex-wrap:wrap;grid-column:1 / -1;}
        .search-box{display:flex;align-items:center;gap:10px;background:var(--bg);border:1.5px solid var(--border);border-radius:14px;padding:12px 14px;}
        .search-box input{width:100%;border:none;outline:none;background:transparent;font:500 13px 'Poppins',sans-serif;color:var(--text);}
        .table-wrap{overflow-x:auto;border-radius:16px;border:1px solid var(--border);}
        table{width:100%;border-collapse:collapse;background:#fff;}
        thead th{background:var(--bg);padding:14px 16px;text-align:left;border-bottom:1px solid var(--border);font-size:11px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;color:var(--gray);}
        tbody td{padding:16px;border-bottom:1px solid var(--border);font-size:13px;color:var(--text);vertical-align:middle;}
        tbody tr:last-child td{border-bottom:none;}
        .cell-strong{font-weight:700;}
        .muted{color:var(--gray);}
        .empty-state{padding:48px 20px;text-align:center;color:var(--gray);}
        .empty-state i{font-size:38px;display:block;margin-bottom:12px;opacity:.5;}
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
        @media(max-width:1200px){.summary-grid{grid-template-columns:repeat(3,minmax(0,1fr));}.toolbar-form{grid-template-columns:repeat(2,minmax(0,1fr));}}
        @media(max-width:860px){body{display:block;}.sidebar{position:relative;width:100%;height:auto;}.main{margin-left:0;}.summary-grid,.toolbar-form,.form-grid{grid-template-columns:1fr;}.topbar{padding:0 18px;height:auto;min-height:72px;flex-wrap:wrap;padding-top:12px;padding-bottom:12px;}.content{padding:20px 18px 32px;}}
    </style>
    @stack('styles')
</head>
<body>
<aside class="sidebar">
    <div class="sb-brand">
        <div class="sb-logo"><img src="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}" alt="BC"></div>
        <div>
            <strong>BC Inmobiliaria</strong>
            <span>Panel Principal</span>
        </div>
    </div>

    <div>
        <div class="sb-section-title">General</div>
        <div class="sb-links">
            <a href="{{ route('admin.dashboard') }}" class="sb-link {{ ($currentModule ?? '') === 'dashboard' ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-table-cells-large"></i></span>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <div>
        <div class="sb-section-title">Proyectos</div>
        <div class="sb-links">
            @forelse(($sidebarProjects ?? collect()) as $sidebarProject)
            <a href="{{ route('admin.proyectos.dashboard', $sidebarProject) }}" class="sb-link sb-project-link">
                <span class="sb-icon"><i class="fas fa-building"></i></span>
                <span class="sb-project-meta">
                    <strong>{{ $sidebarProject->nombre }}</strong>
                    <span>{{ $sidebarProject->ubicacion ?: 'Ubicacion por definir' }}</span>
                </span>
                <span class="sb-project-badge">{{ $sidebarProject->lotes_count ?? 0 }}</span>
            </a>
            @empty
            <div class="sb-project-empty">
                Todavia no hay proyectos creados.
            </div>
            @endforelse
        </div>
    </div>

    <div>
        <div class="sb-section-title">Administracion</div>
        <div class="sb-links">
            <a href="{{ route('admin.usuarios.index') }}" class="sb-link {{ ($currentModule ?? '') === 'usuarios' ? 'active' : '' }}">
                <span class="sb-icon"><i class="fas fa-users-cog"></i></span>
                <span>Usuarios del Sistema</span>
            </a>
            <span class="sb-link disabled">
                <span class="sb-icon"><i class="fas fa-user-shield"></i></span>
                <span>Gestion Permisos</span>
            </span>
        </div>
    </div>

    <div class="sb-footer">
        @auth
        <div class="sb-user">
            <div class="sb-avatar">{{ auth()->user()->initials ?: 'U' }}</div>
            <div>
                <strong>{{ auth()->user()->name }}</strong>
                <span>{{ auth()->user()->role?->nombre ?: 'Sin rol' }}</span>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sb-logout-btn"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
        </form>
        @else
        <a href="{{ route('login') }}" class="sb-logout-btn" style="text-decoration:none;"><i class="fas fa-sign-in-alt"></i> Iniciar sesión</a>
        @endauth
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <div class="topbar-title">{!! trim($__env->yieldContent('topbar_title', 'Panel <span>Principal</span>')) !!}</div>
        <div class="topbar-right">
            <div class="topbar-chip"><i class="fas fa-calendar-alt"></i> {{ now('America/Lima')->translatedFormat('D d M Y') }}</div>
            @auth
            <div class="topbar-user">
                <div class="sb-avatar" style="width:36px;height:36px;">{{ auth()->user()->initials ?: 'U' }}</div>
                <div>
                    <strong style="display:block;font-size:13px;line-height:1.2;">{{ auth()->user()->name }}</strong>
                    <span style="display:block;font-size:11px;color:var(--gray);">{{ auth()->user()->role?->nombre ?: 'Sin rol' }}</span>
                </div>
            </div>
            @endauth
        </div>
    </header>

    <main class="content">
        @php($layoutErrors = $errors ?? new \Illuminate\Support\ViewErrorBag())
        @if(session('success'))
        <div class="flash success"><i class="fas fa-check-circle"></i><span>{{ session('success') }}</span></div>
        @endif
        @if($layoutErrors->any())
        <div class="flash error"><i class="fas fa-exclamation-circle"></i><span>{{ $layoutErrors->first() }}</span></div>
        @endif

        <header class="page-header">
            <div>
                <div class="breadcrumbs">
                    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                    <span><i class="fas fa-chevron-right"></i></span>
                    <span>@yield('module_label', 'Modulo')</span>
                </div>
                <h1 class="page-title">@yield('page_title')</h1>
                <p class="page-subtitle">@yield('page_subtitle')</p>
            </div>
            <div class="page-actions">
                @yield('page_actions')
            </div>
        </header>

        @yield('content')
    </main>
</div>

@stack('scripts')
</body>
</html>
