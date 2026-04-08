<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Beatriz Campos Inmobiliaria</title>
    <link rel="icon" type="image/png" href="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}">
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
            --sidebar-w:270px;
            --header-h:80px;
            --bg:#f0f2ff;
            --white:#ffffff;
            --border:#e8eaf6;
            --gray:#64748b;
            --text:#1a1a2e;
        }
        html,body{height:100%;font-family:'Poppins',sans-serif;background:var(--bg);color:var(--text);}
        body{display:flex;overflow-x:hidden;}

        /* ========== SIDEBAR ========== */
        .sidebar{
            width:var(--sidebar-w);height:100vh;
            background:var(--dark);
            display:flex;flex-direction:column;
            position:fixed;top:0;left:0;z-index:200;
            transition:transform .3s ease;
            overflow-y:auto;overflow-x:hidden;
        }
        .sidebar::-webkit-scrollbar{width:4px;}
        .sidebar::-webkit-scrollbar-track{background:transparent;}
        .sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:4px;}

        /* brand */
        .sb-brand{
            padding:24px 22px 18px;
            border-bottom:1px solid rgba(255,255,255,.06);
        }
        .sb-brand-inner{display:flex;align-items:center;gap:12px;}
        .sb-logo{
            width:46px;height:46px;border-radius:13px;
            background:#ffffff;
            display:flex;align-items:center;justify-content:center;
            overflow:hidden;flex-shrink:0;
            box-shadow:0 6px 18px rgba(238,0,187,.35);
        }
        .sb-logo img{width:100%;height:100%;object-fit:contain;padding:4px;}
        .sb-brand-text{}
        .sb-brand-text .sb-name{
            display:block;font-size:13.5px;font-weight:800;
            color:#fff;line-height:1.2;
        }
        .sb-brand-text .sb-role{
            display:block;font-size:9.5px;font-weight:600;
            color:var(--mg);letter-spacing:2px;text-transform:uppercase;margin-top:2px;
        }

        /* search */
        .sb-search{padding:16px 18px 8px;}
        .sb-search-wrap{
            display:flex;align-items:center;gap:9px;
            background:rgba(255,255,255,.06);
            border:1px solid rgba(255,255,255,.08);
            border-radius:12px;padding:9px 14px;
            transition:.2s;
        }
        .sb-search-wrap:focus-within{border-color:rgba(238,0,187,.4);background:rgba(238,0,187,.06);}
        .sb-search-wrap i{color:rgba(255,255,255,.3);font-size:13px;}
        .sb-search-wrap input{
            background:none;border:none;outline:none;
            font-family:'Poppins',sans-serif;font-size:12.5px;
            color:rgba(255,255,255,.75);width:100%;
        }
        .sb-search-wrap input::placeholder{color:rgba(255,255,255,.25);}

        /* nav */
        .sb-nav{flex:1;padding:8px 12px 16px;}
        .sb-section{margin-bottom:6px;}
        .sb-section-title{
            font-size:9.5px;font-weight:700;letter-spacing:1.8px;
            text-transform:uppercase;color:rgba(255,255,255,.25);
            padding:10px 10px 6px;
        }
        .sb-link{
            display:flex;align-items:center;gap:12px;
            padding:10px 12px;border-radius:12px;
            text-decoration:none;color:rgba(255,255,255,.55);
            font-size:13px;font-weight:500;
            transition:all .2s;margin-bottom:2px;
            position:relative;
        }
        .sb-link:hover{background:rgba(255,255,255,.06);color:rgba(255,255,255,.85);}
        .sb-link.active{
            background:linear-gradient(135deg,rgba(238,0,187,.18),rgba(85,51,204,.18));
            color:#fff;
        }
        .sb-link.active::before{
            content:'';position:absolute;left:0;top:20%;bottom:20%;
            width:3px;background:linear-gradient(var(--mg),var(--vt));
            border-radius:0 3px 3px 0;
        }
        .sb-link .sb-icon{
            width:34px;height:34px;border-radius:10px;flex-shrink:0;
            display:flex;align-items:center;justify-content:center;
            font-size:15px;background:rgba(255,255,255,.06);
            transition:.2s;
        }
        .sb-link.active .sb-icon{background:linear-gradient(135deg,var(--mg),var(--vt));color:#fff;}
        .sb-link:hover .sb-icon{background:rgba(255,255,255,.1);}
        .sb-link span{flex:1;}
        .sb-badge{
            background:var(--mg);color:#fff;
            font-size:10px;font-weight:700;
            padding:2px 8px;border-radius:50px;
        }

        /* footer sidebar */
        .sb-footer{
            padding:14px 18px;
            border-top:1px solid rgba(255,255,255,.06);
        }
        .sb-user{
            display:flex;align-items:center;gap:11px;
            padding:10px 12px;border-radius:12px;
            background:rgba(255,255,255,.05);
            text-decoration:none;transition:.2s;
        }
        .sb-user:hover{background:rgba(255,255,255,.09);}
        .sb-avatar{
            width:36px;height:36px;border-radius:50%;
            background:linear-gradient(135deg,var(--mg),var(--vt));
            display:flex;align-items:center;justify-content:center;
            color:#fff;font-size:14px;font-weight:700;flex-shrink:0;
        }
        .sb-user-info .su-name{display:block;font-size:12.5px;font-weight:600;color:#fff;}
        .sb-user-info .su-role{display:block;font-size:10.5px;color:rgba(255,255,255,.4);}

        /* ========== MAIN ========== */
        .main{
            margin-left:var(--sidebar-w);
            flex:1;display:flex;flex-direction:column;min-height:100vh;
        }

        /* header */
        .topbar{
            height:var(--header-h);
            background:var(--white);
            border-bottom:2px solid var(--border);
            display:flex;align-items:center;justify-content:space-between;
            padding:0 32px;
            position:sticky;top:0;z-index:100;
            box-shadow:0 4px 20px rgba(0,0,0,.06);
        }
        .topbar-left{display:flex;align-items:center;gap:14px;}
        .sb-toggle{
            width:38px;height:38px;border-radius:10px;
            background:var(--bg);border:none;cursor:pointer;
            display:flex;align-items:center;justify-content:center;
            font-size:17px;color:var(--gray);transition:.2s;
        }
        .sb-toggle:hover{background:var(--border);color:var(--text);}
        .topbar-title{font-size:17px;font-weight:700;color:var(--text);}
        .topbar-title span{color:var(--mg);}

        /* search topbar */
        .tb-search{
            display:flex;align-items:center;gap:9px;
            background:var(--bg);border:1.5px solid var(--border);
            border-radius:12px;padding:10px 16px;
            transition:.2s;
        }
        .tb-search:focus-within{border-color:rgba(238,0,187,.4);background:#fff;box-shadow:0 0 0 3px rgba(238,0,187,.06);}
        .tb-search i{font-size:13px;color:var(--gray);}
        .tb-search input{
            background:none;border:none;outline:none;
            font-family:'Poppins',sans-serif;font-size:13px;
            color:var(--text);width:200px;
        }
        .tb-search input::placeholder{color:#b0bac9;}

        .topbar-right{display:flex;align-items:center;gap:10px;}

        /* fecha */
        .tb-date{
            font-size:12.5px;font-weight:500;color:var(--gray);
            background:var(--bg);border:1.5px solid var(--border);
            border-radius:12px;padding:10px 16px;
            display:flex;align-items:center;gap:8px;white-space:nowrap;
        }
        .tb-date i{color:var(--mg);font-size:13px;}

        .tb-icon-btn{
            width:42px;height:42px;border-radius:12px;
            background:var(--bg);border:1.5px solid var(--border);cursor:pointer;
            display:flex;align-items:center;justify-content:center;
            font-size:16px;color:var(--gray);transition:.2s;position:relative;
        }
        .tb-icon-btn:hover{background:var(--border);color:var(--text);}
        .tb-notif::after{
            content:'3';position:absolute;top:5px;right:5px;
            width:17px;height:17px;border-radius:50%;
            background:var(--mg);color:#fff;font-size:9px;font-weight:700;
            display:flex;align-items:center;justify-content:center;
        }

        /* usuario */
        .tb-user{
            display:flex;align-items:center;gap:10px;
            background:var(--bg);border-radius:14px;padding:7px 14px 7px 7px;
            cursor:pointer;border:1.5px solid var(--border);transition:.2s;
        }
        .tb-user:hover{border-color:rgba(238,0,187,.35);background:#fff;box-shadow:0 4px 14px rgba(238,0,187,.08);}
        .tb-avatar{
            width:36px;height:36px;border-radius:50%;
            background:linear-gradient(135deg,var(--mg),var(--vt));
            display:flex;align-items:center;justify-content:center;
            color:#fff;font-size:13px;font-weight:700;flex-shrink:0;
            box-shadow:0 3px 10px rgba(238,0,187,.3);
        }
        .tb-user-info{display:flex;flex-direction:column;}
        .tb-name{font-size:13px;font-weight:700;color:var(--text);line-height:1.3;}
        .tb-role{font-size:11px;color:var(--gray);font-weight:400;}

        /* logout */
        .tb-logout{
            display:flex;align-items:center;gap:8px;
            background:linear-gradient(135deg,rgba(238,0,187,.1),rgba(85,51,204,.08));
            border:1.5px solid rgba(238,0,187,.3);
            color:var(--mg);padding:10px 20px;border-radius:12px;
            font-size:13px;font-weight:600;text-decoration:none;
            transition:.2s;white-space:nowrap;
        }
        .tb-logout:hover{background:linear-gradient(135deg,var(--mg),var(--vt));color:#fff;border-color:transparent;box-shadow:0 6px 18px rgba(238,0,187,.35);transform:translateY(-1px);}

        /* content */
        .content{padding:32px;flex:1;}

        /* welcome banner */
        .banner{
            background:linear-gradient(135deg,var(--dark2) 0%,var(--dark) 40%,#2d1b69 70%,var(--vt2) 100%);
            border-radius:22px;padding:32px 36px;
            display:flex;align-items:center;justify-content:space-between;
            margin-bottom:28px;position:relative;overflow:hidden;
        }
        .banner::before{
            content:'';position:absolute;right:-60px;top:-60px;
            width:280px;height:280px;border-radius:50%;
            background:radial-gradient(circle,rgba(238,0,187,.2) 0%,transparent 70%);
        }
        .banner-text{}
        .banner-text h2{font-size:24px;font-weight:800;color:#fff;margin-bottom:6px;}
        .banner-text h2 em{font-style:normal;color:var(--mg);}
        .banner-text p{font-size:13.5px;color:rgba(255,255,255,.6);line-height:1.6;max-width:480px;}
        .banner-btn{
            display:flex;align-items:center;gap:8px;flex-shrink:0;
            background:linear-gradient(135deg,var(--mg),var(--mg2));
            color:#fff;padding:12px 24px;border-radius:50px;
            font-size:13px;font-weight:700;text-decoration:none;
            box-shadow:0 8px 24px rgba(238,0,187,.4);transition:.3s;
            position:relative;z-index:1;border:none;cursor:pointer;
        }
        .banner-btn:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(238,0,187,.5);}

        .flash{
            display:flex;align-items:center;gap:10px;
            padding:14px 18px;border-radius:16px;
            margin-bottom:18px;font-size:13px;font-weight:600;
            border:1px solid transparent;
        }
        .flash.success{background:#ecfdf5;color:#047857;border-color:#a7f3d0;}
        .flash.error{background:#fff1f2;color:#be123c;border-color:#fecdd3;}

        .modal-overlay{
            position:fixed;inset:0;z-index:1200;
            background:rgba(26,26,46,.55);
            backdrop-filter:blur(4px);
            display:none;align-items:center;justify-content:center;
            padding:18px;
        }
        .modal-overlay.open{display:flex;}
        .modal-card{
            width:min(100%, 480px);
            background:#fff;border-radius:24px;
            border:1px solid var(--border);
            box-shadow:0 24px 64px rgba(15,23,42,.22);
            overflow:hidden;
        }
        .modal-head{
            padding:22px 24px 14px;
            display:flex;align-items:flex-start;justify-content:space-between;gap:18px;
        }
        .modal-title{
            font-size:20px;font-weight:800;color:var(--text);line-height:1.2;
        }
        .modal-title span{color:var(--mg);}
        .modal-subtitle{
            margin-top:6px;font-size:12.5px;color:var(--gray);line-height:1.6;
        }
        .modal-close{
            width:38px;height:38px;border-radius:12px;
            border:1px solid var(--border);background:var(--bg);
            color:var(--gray);cursor:pointer;transition:.2s;font-size:14px;
        }
        .modal-close:hover{background:#fff;color:var(--text);border-color:rgba(238,0,187,.3);}
        .modal-body{padding:0 24px 22px;}
        .form-label{
            display:block;margin-bottom:8px;
            font-size:12px;font-weight:700;color:var(--text);
            text-transform:uppercase;letter-spacing:.7px;
        }
        .form-label span{color:var(--mg);}
        .form-control{
            width:100%;border:1.5px solid var(--border);
            background:var(--bg);border-radius:14px;
            padding:14px 16px;font:500 14px 'Poppins',sans-serif;color:var(--text);
            outline:none;transition:.2s;
        }
        .form-control:focus{
            border-color:rgba(238,0,187,.45);
            background:#fff;box-shadow:0 0 0 4px rgba(238,0,187,.08);
        }
        .field-error{
            margin-top:8px;font-size:12px;font-weight:600;color:#be123c;
        }
        .modal-note{
            margin-top:14px;padding:12px 14px;border-radius:14px;
            background:linear-gradient(135deg,rgba(85,51,204,.08),rgba(238,0,187,.08));
            color:var(--gray);font-size:12.5px;line-height:1.6;
        }
        .modal-actions{
            margin-top:18px;display:flex;justify-content:flex-end;gap:10px;
        }
        .btn-secondary{
            border:1.5px solid var(--border);background:#fff;color:var(--gray);
            padding:11px 18px;border-radius:14px;
            font:600 13px 'Poppins',sans-serif;cursor:pointer;transition:.2s;
        }
        .btn-secondary:hover{border-color:rgba(85,51,204,.3);color:var(--vt);}
        .btn-primary{
            border:none;background:linear-gradient(135deg,var(--mg),var(--vt));
            color:#fff;padding:11px 20px;border-radius:14px;
            font:700 13px 'Poppins',sans-serif;cursor:pointer;transition:.2s;
            box-shadow:0 10px 24px rgba(85,51,204,.22);
        }
        .btn-primary:hover{transform:translateY(-1px);box-shadow:0 14px 30px rgba(85,51,204,.28);}

        /* stat cards */
        .stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:28px;}
        .stat-card{
            background:var(--white);border-radius:18px;padding:24px 26px;
            display:flex;align-items:center;gap:18px;
            border:1px solid var(--border);
            box-shadow:0 2px 12px rgba(0,0,0,.04);
            transition:all .3s;
        }
        .stat-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(0,0,0,.09);}
        .stat-icon{
            width:56px;height:56px;border-radius:16px;flex-shrink:0;
            display:flex;align-items:center;justify-content:center;font-size:22px;
        }
        .si-mg{background:linear-gradient(135deg,rgba(238,0,187,.12),rgba(238,0,187,.06));color:var(--mg);}
        .si-vt{background:linear-gradient(135deg,rgba(85,51,204,.12),rgba(85,51,204,.06));color:var(--vt);}
        .si-gn{background:linear-gradient(135deg,rgba(16,185,129,.12),rgba(16,185,129,.06));color:#10b981;}
        .stat-info{}
        .stat-num{font-size:32px;font-weight:900;color:var(--mg);line-height:1;}
        .stat-num.vt{color:var(--vt);}
        .stat-num.gn{color:#10b981;}
        .stat-lbl{font-size:11.5px;font-weight:600;color:var(--gray);letter-spacing:.5px;text-transform:uppercase;margin-top:4px;}

        /* section headers */
        .sec-head{
            display:flex;align-items:center;justify-content:space-between;
            margin-bottom:18px;
        }
        .sec-title{font-size:16px;font-weight:700;color:var(--text);}
        .sec-title span{color:var(--mg);}
        .sec-link{font-size:12.5px;color:var(--mg);font-weight:600;text-decoration:none;transition:.2s;}
        .sec-link:hover{color:var(--mg2);}

        /* tool cards */
        .tools-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:28px;}
        .tool-card{
            background:var(--white);border-radius:18px;padding:24px 22px;
            border:1px solid var(--border);
            box-shadow:0 2px 12px rgba(0,0,0,.04);
            transition:all .3s;cursor:pointer;text-decoration:none;
            display:flex;flex-direction:column;gap:14px;
        }
        .tool-card:hover{transform:translateY(-6px);box-shadow:0 16px 40px rgba(0,0,0,.09);border-color:rgba(238,0,187,.2);}
        .tool-icon{
            width:50px;height:50px;border-radius:14px;
            display:flex;align-items:center;justify-content:center;
            font-size:20px;color:#fff;
        }
        .ti-mg{background:linear-gradient(135deg,var(--mg),var(--mg2));}
        .ti-vt{background:linear-gradient(135deg,var(--vt),var(--vt2));}
        .ti-gn{background:linear-gradient(135deg,#10b981,#059669);}
        .ti-bl{background:linear-gradient(135deg,#3b82f6,#1d4ed8);}
        .ti-or{background:linear-gradient(135deg,#f59e0b,#d97706);}
        .ti-re{background:linear-gradient(135deg,#ef4444,#dc2626);}
        .tool-name{font-size:14px;font-weight:700;color:var(--text);}
        .tool-desc{font-size:12px;color:var(--gray);line-height:1.6;}

        /* proyectos tabla */
        .projects-table{
            background:var(--white);border-radius:18px;
            border:1px solid var(--border);
            box-shadow:0 2px 12px rgba(0,0,0,.04);
            overflow:hidden;margin-bottom:28px;
        }
        .pt-head{
            display:grid;grid-template-columns:2fr 1fr 1fr 1fr 100px;
            padding:14px 24px;
            background:var(--bg);border-bottom:1px solid var(--border);
            font-size:11px;font-weight:700;color:var(--gray);
            letter-spacing:.8px;text-transform:uppercase;
        }
        .pt-row{
            display:grid;grid-template-columns:2fr 1fr 1fr 1fr 100px;
            padding:16px 24px;align-items:center;
            border-bottom:1px solid var(--border);transition:.2s;
        }
        .pt-row:last-child{border-bottom:none;}
        .pt-row:hover{background:#fafaff;}
        .pt-proj{display:flex;align-items:center;gap:12px;}
        .pt-ico{
            width:38px;height:38px;border-radius:11px;flex-shrink:0;
            display:flex;align-items:center;justify-content:center;font-size:16px;color:#fff;
        }
        .pt-proj-name{font-size:13.5px;font-weight:600;color:var(--text);}
        .pt-proj-loc{font-size:11px;color:var(--gray);}
        .pt-cell{font-size:13px;color:var(--text);}
        .pt-price{font-size:14px;font-weight:700;color:var(--mg);}
        .pt-badge{
            display:inline-flex;align-items:center;gap:5px;
            font-size:11px;font-weight:600;padding:4px 12px;border-radius:50px;
        }
        .pb-active{background:rgba(16,185,129,.1);color:#059669;}
        .pb-new{background:rgba(238,0,187,.1);color:var(--mg2);}
        .pt-btn{
            display:inline-flex;align-items:center;gap:6px;
            font-size:12px;font-weight:600;color:var(--vt);
            background:rgba(85,51,204,.08);padding:6px 14px;
            border-radius:50px;text-decoration:none;transition:.2s;border:none;cursor:pointer;
        }
        .pt-btn:hover{background:rgba(85,51,204,.15);}

        /* ========== RESPONSIVE ========== */
        @media(max-width:1200px){
            .tools-grid{grid-template-columns:repeat(3,1fr);}
        }
        @media(max-width:1024px){
            :root{--sidebar-w:240px;}
            .stats-grid{grid-template-columns:1fr 1fr;}
            .tools-grid{grid-template-columns:repeat(2,1fr);}
            .pt-head,.pt-row{grid-template-columns:2fr 1fr 1fr 100px;display:grid;}
            .pt-head>*:nth-child(3),.pt-row>*:nth-child(3){display:none;}
        }
        @media(max-width:768px){
            .sidebar{transform:translateX(-100%);}
            .sidebar.open{transform:translateX(0);}
            .main{margin-left:0;}
            .topbar{padding:0 20px;}
            .content{padding:20px 16px;}
            .stats-grid{grid-template-columns:1fr;}
            .tools-grid{grid-template-columns:1fr 1fr;}
            .banner{flex-direction:column;gap:20px;text-align:center;}
            .banner-text p{max-width:100%;}
        }
        @media(max-width:480px){
            .tools-grid{grid-template-columns:1fr;}
        }
    </style>
</head>
<body>

<!-- ========== SIDEBAR ========== -->
<aside class="sidebar" id="sidebar">

    <div class="sb-brand">
        <div class="sb-brand-inner">
            <div class="sb-logo">
                <img src="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}" alt="Logo">
            </div>
            <div class="sb-brand-text">
                <span class="sb-name">Beatriz Campos</span>
                <span class="sb-role">Inmobiliaria</span>
            </div>
        </div>
    </div>

    <div class="sb-search">
        <div class="sb-search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Buscar...">
        </div>
    </div>

    <nav class="sb-nav">

        <div class="sb-section">
            <div class="sb-section-title">General</div>
            <a href="#" class="sb-link active">
                <div class="sb-icon"><i class="fas fa-th-large"></i></div>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="sb-section">
            <div class="sb-section-title">Proyectos</div>
            @foreach($proyectos as $proyecto)
            <a href="{{ route('admin.proyectos.show', $proyecto) }}" class="sb-link">
                <div class="sb-icon"><i class="fas fa-home"></i></div>
                <span>{{ $proyecto->nombre }}</span>
                @if($proyecto->precio_base)
                <span class="sb-badge">{{ number_format($proyecto->precio_base / 1000, 0) }}k</span>
                @endif
            </a>
            @endforeach
        </div>

        <div class="sb-section">
            <div class="sb-section-title">Gestión</div>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-users"></i></div>
                <span>Clientes</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-file-contract"></i></div>
                <span>Contratos</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-hand-holding-usd"></i></div>
                <span>Pagos y Cuotas</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-minus-circle"></i></div>
                <span>Egresos Generales</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-clipboard-list"></i></div>
                <span>Requerimientos General</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-clipboard"></i></div>
                <span>Requerimientos Caja Chica</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-mobile-alt"></i></div>
                <span>Pago Clientes Virtuales</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-calendar-alt"></i></div>
                <span>Fechas de Vencimiento</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-university"></i></div>
                <span>Caja General</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-cash-register"></i></div>
                <span>Caja Chica</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-user-friends"></i></div>
                <span>Clientes Totales</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-drafting-compass"></i></div>
                <span>Arquitectura</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-sitemap"></i></div>
                <span>Gestión Planos Clientes</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-cube"></i></div>
                <span>Subir Plano 3D</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-user-plus"></i></div>
                <span>Gestión de Leads</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-file-excel"></i></div>
                <span>Excel Boletas</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-headset"></i></div>
                <span>CRM</span>
            </a>
        </div>

        <div class="sb-section">
            <div class="sb-section-title">Contabilidad</div>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-calculator"></i></div>
                <span>Contabilidad General</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <span>Datos Contables</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-id-badge"></i></div>
                <span>Planilla</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-truck"></i></div>
                <span>Proveedores</span>
            </a>
        </div>

        <div class="sb-section">
            <div class="sb-section-title">Marketing &amp; Analytics</div>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-chart-line"></i></div>
                <span>Marketing</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-tachometer-alt"></i></div>
                <span>Dashboard General</span>
            </a>
        </div>

        <div class="sb-section">
            <div class="sb-section-title">Inventario Oficina</div>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-boxes"></i></div>
                <span>Inventario Oficina</span>
            </a>
        </div>

        <div class="sb-section">
            <div class="sb-section-title">Permisos</div>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-user-shield"></i></div>
                <span>Gestión Permisos</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-folder-open"></i></div>
                <span>Historial de Documentos</span>
            </a>
            <a href="#" class="sb-link">
                <div class="sb-icon"><i class="fas fa-users-cog"></i></div>
                <span>Usuarios del Sistema</span>
            </a>
        </div>

        <div class="sb-section">
            <div class="sb-section-title">Cuenta</div>
            <a href="{{ url('/') }}" class="sb-link">
                <div class="sb-icon"><i class="fas fa-globe"></i></div>
                <span>Ir al Sitio Web</span>
            </a>
            <a href="{{ url('/acceso') }}" class="sb-link" style="color:rgba(238,0,187,.7);">
                <div class="sb-icon" style="color:var(--mg);"><i class="fas fa-sign-out-alt"></i></div>
                <span>Cerrar Sesión</span>
            </a>
        </div>

    </nav>

    <div class="sb-footer">
        <div class="sb-user">
            <div class="sb-avatar">A</div>
            <div class="sb-user-info">
                <span class="su-name">Administrador</span>
                <span class="su-role">admin@bcinmobiliaria.com</span>
            </div>
        </div>
    </div>

</aside>

<!-- ========== MAIN ========== -->
<div class="main">

    <!-- TOPBAR -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="sb-toggle" id="sbToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-title">Dashboard <span>General</span></div>
            <div class="tb-search">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar módulo, cliente...">
            </div>
        </div>
        <div class="topbar-right">
            <div class="tb-date">
                <i class="fas fa-calendar-alt"></i>
                <span id="tbDate"></span>
            </div>
            <button class="tb-icon-btn tb-notif" title="Notificaciones">
                <i class="fas fa-bell"></i>
            </button>
            <button class="tb-icon-btn" title="Configuración">
                <i class="fas fa-cog"></i>
            </button>
            <div class="tb-user">
                <div class="tb-avatar">A</div>
                <div class="tb-user-info">
                    <span class="tb-name">Administrador BC</span>
                    <span class="tb-role">Super Admin</span>
                </div>
                <i class="fas fa-chevron-down" style="font-size:10px;color:var(--gray);margin-left:4px;"></i>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="content">
        @if(session('success'))
        <div class="flash success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="flash error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif
 
        <!-- Banner bienvenida -->
        <div class="banner">
            <div class="banner-text">
                <h2>Bienvenido al <em>Panel de Gestión</em></h2>
                <p>Administra todos los proyectos, clientes y contratos de Beatriz Campos Inmobiliaria desde un solo lugar.</p>
            </div>
            <button type="button" class="banner-btn" onclick="openNuevoProyectoModal()">
                <i class="fas fa-plus"></i> Nuevo Proyecto
            </button>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon si-mg"><i class="fas fa-building"></i></div>
                <div class="stat-info">
                    <div class="stat-num">{{ $proyectos->where('estado', 'activo')->count() }}</div>
                    <div class="stat-lbl">Proyectos Activos</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon si-vt"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <div class="stat-num vt">252</div>
                    <div class="stat-lbl">Familias Atendidas</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon si-gn"><i class="fas fa-file-contract"></i></div>
                <div class="stat-info">
                    <div class="stat-num gn">0%</div>
                    <div class="stat-lbl">Intereses Aplicados</div>
                </div>
            </div>
        </div>

        <!-- Proyectos tabla -->
        <div class="sec-head">
            <div class="sec-title">Proyectos <span>Disponibles</span></div>
            <a href="#" class="sec-link">Ver todos <i class="fas fa-arrow-right" style="font-size:11px;"></i></a>
        </div>
        <div class="projects-table">
            <div class="pt-head">
                <div>Proyecto</div>
                <div>Precio</div>
                <div>Lotes</div>
                <div>Estado</div>
                <div>Acción</div>
            </div>
            @php
                $iconoClases = ['ti-mg','ti-vt','ti-or'];
                $iconos      = ['fas fa-home','fas fa-road','fas fa-map-marker-alt'];
                $idx = 0;
            @endphp
            @isset($proyectos)
                @foreach($proyectos as $proyecto)
                @php $cls = $iconoClases[$idx % 3]; $ico = $iconos[$idx % 3]; $idx++; @endphp
                <div class="pt-row">
                    <div class="pt-proj">
                        <div class="pt-ico {{ $cls }}"><i class="{{ $ico }}"></i></div>
                        <div>
                            <div class="pt-proj-name">{{ $proyecto->nombre }}</div>
                            <div class="pt-proj-loc"><i class="fas fa-map-marker-alt" style="color:var(--mg);font-size:10px;"></i> {{ $proyecto->ubicacion }}</div>
                        </div>
                    </div>
                    <div class="pt-price">S/. {{ number_format($proyecto->precio_base, 0, '.', ',') }}</div>
                    <div class="pt-cell">{{ $proyecto->lotes_count ?? 0 }} lotes</div>
                    <div><span class="pt-badge {{ $proyecto->estado === 'activo' ? 'pb-active' : 'pb-new' }}"><i class="fas fa-circle" style="font-size:7px;"></i> {{ ucfirst($proyecto->estado) }}</span></div>
                    <div><a href="{{ route('admin.proyectos.show', $proyecto) }}" class="pt-btn"><i class="fas fa-eye"></i> Ver</a></div>
                </div>
                @endforeach
            @else
                <div class="pt-row">
                    <div class="pt-proj">
                        <div class="pt-ico ti-mg"><i class="fas fa-home"></i></div>
                        <div>
                            <div class="pt-proj-name">Lotes Hualhuas</div>
                            <div class="pt-proj-loc"><i class="fas fa-map-marker-alt" style="color:var(--mg);font-size:10px;"></i> Av. 13 de Diciembre, Hualhuas</div>
                        </div>
                    </div>
                    <div class="pt-price">S/. 35,000</div>
                    <div class="pt-cell">10 lotes</div>
                    <div><span class="pt-badge pb-new"><i class="fas fa-circle" style="font-size:7px;"></i> Activo</span></div>
                    <div><button class="pt-btn"><i class="fas fa-eye"></i> Ver</button></div>
                </div>
            @endisset
        </div>

        <!-- Herramientas -->
        <div class="sec-head">
            <div class="sec-title">Herramientas de <span>Gestión</span></div>
        </div>
        <div class="tools-grid">
            <a href="#" class="tool-card">
                <div class="tool-icon ti-mg"><i class="fas fa-chart-pie"></i></div>
                <div>
                    <div class="tool-name">Estadísticas</div>
                    <div class="tool-desc">Vista general de métricas de ventas y proyectos.</div>
                </div>
            </a>
            <a href="#" class="tool-card">
                <div class="tool-icon ti-vt"><i class="fas fa-users"></i></div>
                <div>
                    <div class="tool-name">Gestión de Clientes</div>
                    <div class="tool-desc">Administra la base de datos de clientes registrados.</div>
                </div>
            </a>
            <a href="#" class="tool-card">
                <div class="tool-icon ti-gn"><i class="fas fa-file-contract"></i></div>
                <div>
                    <div class="tool-name">Contratos</div>
                    <div class="tool-desc">Genera y gestiona contratos de compraventa.</div>
                </div>
            </a>
            <a href="#" class="tool-card">
                <div class="tool-icon ti-bl"><i class="fas fa-hand-holding-usd"></i></div>
                <div>
                    <div class="tool-name">Pagos y Cuotas</div>
                    <div class="tool-desc">Control de financiamiento y cuotas de clientes.</div>
                </div>
            </a>
            <a href="#" class="tool-card">
                <div class="tool-icon ti-or"><i class="fas fa-calendar-alt"></i></div>
                <div>
                    <div class="tool-name">Vencimientos</div>
                    <div class="tool-desc">Control de fechas y vencimientos importantes.</div>
                </div>
            </a>
            <a href="#" class="tool-card">
                <div class="tool-icon ti-re"><i class="fas fa-receipt"></i></div>
                <div>
                    <div class="tool-name">Egresos Generales</div>
                    <div class="tool-desc">Gestión de gastos y egresos de la empresa.</div>
                </div>
            </a>
            <a href="#" class="tool-card">
                <div class="tool-icon ti-mg"><i class="fas fa-map-marked-alt"></i></div>
                <div>
                    <div class="tool-name">Mapa de Lotes</div>
                    <div class="tool-desc">Visualiza la disponibilidad de lotes por proyecto.</div>
                </div>
            </a>
            <a href="#" class="tool-card">
                <div class="tool-icon ti-vt"><i class="fas fa-chart-line"></i></div>
                <div>
                    <div class="tool-name">Reportes</div>
                    <div class="tool-desc">Genera reportes de ventas, ingresos y proyecciones.</div>
                </div>
            </a>
        </div>

    </main>
</div>

<div class="modal-overlay{{ $errors->has('nombre') ? ' open' : '' }}" id="nuevoProyectoModal">
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
                <label for="nuevoProyectoNombre" class="form-label">Nombre del proyecto <span>*</span></label>
                <input
                    type="text"
                    id="nuevoProyectoNombre"
                    name="nombre"
                    class="form-control"
                    placeholder="Ej: Residencial Las Lomas"
                    value="{{ old('nombre') }}"
                    maxlength="150"
                    required
                >
                @error('nombre')
                <div class="field-error">{{ $message }}</div>
                @enderror

                <div class="modal-note">
                    Se crearán automáticamente el código, slug, panel del proyecto y la estructura base para lotes, clientes, cobranza, ingresos, egresos, caja y documentos.
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-secondary" onclick="closeNuevoProyectoModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">Crear proyecto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const nuevoProyectoModal = document.getElementById('nuevoProyectoModal');
    const nuevoProyectoNombre = document.getElementById('nuevoProyectoNombre');

    function openNuevoProyectoModal() {
        nuevoProyectoModal.classList.add('open');
        setTimeout(() => nuevoProyectoNombre.focus(), 40);
    }

    function closeNuevoProyectoModal() {
        nuevoProyectoModal.classList.remove('open');
    }

    nuevoProyectoModal.addEventListener('click', (event) => {
        if (event.target === nuevoProyectoModal) {
            closeNuevoProyectoModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeNuevoProyectoModal();
        }
    });

    // Fecha en vivo
    function updateDate(){
        const d = new Date();
        const dias = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
        const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        document.getElementById('tbDate').textContent =
            dias[d.getDay()] + ' ' + d.getDate() + ' ' + meses[d.getMonth()] + ' ' + d.getFullYear();
    }
    updateDate();

    const sbToggle = document.getElementById('sbToggle');
    const sidebar  = document.getElementById('sidebar');
    sbToggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
    // cerrar sidebar en móvil al hacer click fuera
    document.addEventListener('click', (e) => {
        if(window.innerWidth <= 768 &&
           !sidebar.contains(e.target) &&
           !sbToggle.contains(e.target)){
             sidebar.classList.remove('open');
         }
     });
</script>
</body>
</html>
