<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrativo — Beatriz Campos Inmobiliaria</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --pink:        #FF1493;
            --pink-light:  #FF69B4;
            --pink-dark:   #C0006A;
            --purple:      #7B2DD4;
            --purple-dark: #5B1DB4;
            --purple-light:#A855F7;
            --dark:        #1C1B33;
            --dark2:       #2D2B50;
            --sidebar-w:   265px;
            --header-h:    70px;
            --bg:          #F4F6FB;
            --card:        #FFFFFF;
            --border:      #EAECf0;
            --text:        #1C1B33;
            --text-muted:  #8B90A0;
            --radius:      16px;
            --shadow:      0 4px 20px rgba(0,0,0,0.07);
            --shadow-md:   0 8px 30px rgba(0,0,0,0.10);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ===================== SIDEBAR ===================== */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--dark);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
            transition: transform 0.3s ease;
        }

        /* Borde superior de colores */
        .sidebar::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--pink), var(--purple));
        }

        /* Brand / Logo */
        .sidebar-brand {
            padding: 24px 20px 22px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-logo {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--pink), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 6px 20px rgba(255,20,147,0.4);
            overflow: hidden;
        }
        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-info { min-width: 0; }
        .brand-name {
            font-size: 13px;
            font-weight: 700;
            color: white;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .brand-name span { color: var(--pink); }
        .brand-sub {
            font-size: 10px;
            color: rgba(255,255,255,0.4);
            font-weight: 400;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* Navegación */
        .sidebar-nav {
            flex: 1;
            padding: 20px 12px;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.25);
            padding: 0 10px;
            margin: 8px 0 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 12px;
            text-decoration: none;
            color: rgba(255,255,255,0.55);
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 2px;
            transition: all 0.25s ease;
            position: relative;
            cursor: pointer;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.9);
        }

        .nav-item.active {
            background: linear-gradient(135deg, rgba(255,20,147,0.18), rgba(123,45,212,0.18));
            color: white;
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 8px; bottom: 8px;
            width: 3px;
            background: linear-gradient(to bottom, var(--pink), var(--purple));
            border-radius: 0 3px 3px 0;
        }

        .nav-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            transition: all 0.25s;
            background: rgba(255,255,255,0.05);
        }

        .nav-item.active .nav-icon {
            background: linear-gradient(135deg, var(--pink), var(--purple));
            color: white;
            box-shadow: 0 4px 12px rgba(255,20,147,0.35);
        }

        .nav-item:hover .nav-icon {
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--pink);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
        }

        /* Usuario sidebar */
        .sidebar-user {
            padding: 16px 14px;
            margin: 8px 12px 16px;
            background: rgba(255,255,255,0.05);
            border-radius: 14px;
            display: flex;
            align-items: center;
            gap: 11px;
            border: 1px solid rgba(255,255,255,0.07);
        }
        .user-avatar-sm {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--pink), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: white;
            font-weight: 700;
            flex-shrink: 0;
        }
        .user-info-sm { min-width: 0; }
        .user-name-sm {
            font-size: 12.5px;
            font-weight: 600;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-role-sm {
            font-size: 10px;
            color: var(--pink-light);
            font-weight: 400;
        }
        .user-actions { margin-left: auto; }
        .user-actions a {
            color: rgba(255,255,255,0.3);
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
        }
        .user-actions a:hover { color: var(--pink); }

        /* ===================== MAIN ===================== */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* Header */
        .header {
            height: var(--header-h);
            background: var(--card);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 30px;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        .header-left { flex: 1; }
        .page-title {
            font-size: 19px;
            font-weight: 700;
            color: var(--text);
        }
        .page-breadcrumb {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }
        .page-breadcrumb span { color: var(--pink); font-weight: 500; }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--bg);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 15px;
            transition: all 0.2s;
            position: relative;
            text-decoration: none;
        }
        .header-btn:hover {
            background: linear-gradient(135deg, rgba(255,20,147,0.08), rgba(123,45,212,0.08));
            border-color: var(--pink);
            color: var(--pink);
        }

        .notification-dot {
            position: absolute;
            top: 6px; right: 7px;
            width: 8px; height: 8px;
            background: var(--pink);
            border-radius: 50%;
            border: 2px solid white;
        }

        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0 14px;
            height: 40px;
            transition: all 0.2s;
        }
        .search-box:focus-within {
            border-color: var(--pink);
            box-shadow: 0 0 0 3px rgba(255,20,147,0.08);
        }
        .search-box i { color: var(--text-muted); font-size: 13px; }
        .search-box input {
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            color: var(--text);
            width: 180px;
        }
        .search-box input::placeholder { color: var(--text-muted); }

        .header-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 10px 6px 6px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: var(--bg);
            cursor: pointer;
            transition: all 0.2s;
        }
        .header-user:hover {
            border-color: var(--pink);
            background: rgba(255,20,147,0.04);
        }
        .header-avatar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--pink), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            font-weight: 700;
        }
        .header-user-info { line-height: 1.2; }
        .header-user-name { font-size: 13px; font-weight: 600; color: var(--text); }
        .header-user-role { font-size: 10px; color: var(--text-muted); }
        .header-user-arrow { color: var(--text-muted); font-size: 11px; margin-left: 4px; }

        /* ===================== CONTENT ===================== */
        .content {
            padding: 28px 30px;
            flex: 1;
        }

        /* Bienvenida */
        .welcome-banner {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark2) 40%, #3D1060 100%);
            border-radius: 22px;
            padding: 28px 32px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(28,27,51,0.25);
        }
        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 250px; height: 250px;
            border-radius: 50%;
            background: rgba(255,20,147,0.15);
        }
        .welcome-banner::after {
            content: '';
            position: absolute;
            bottom: -80px; right: 120px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(123,45,212,0.15);
        }

        .welcome-text { position: relative; z-index: 2; }
        .welcome-greeting {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--pink-light);
            margin-bottom: 6px;
        }
        .welcome-heading {
            font-size: 24px;
            font-weight: 700;
            color: white;
            margin-bottom: 6px;
        }
        .welcome-heading span { color: var(--pink); }
        .welcome-desc { font-size: 13px; color: rgba(255,255,255,0.55); }

        .welcome-actions { position: relative; z-index: 2; display: flex; gap: 10px; }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            background: linear-gradient(135deg, var(--pink), var(--pink-dark));
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s;
            box-shadow: 0 6px 20px rgba(255,20,147,0.4);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(255,20,147,0.5); }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 20px;
            background: rgba(255,255,255,0.12);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 12px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.25s;
        }
        .btn-secondary:hover { background: rgba(255,255,255,0.2); }

        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            border-radius: var(--radius);
            padding: 24px 22px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: transform 0.25s, box-shadow 0.25s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.15);
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 120px; height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
        }

        .stat-card-1 { background: linear-gradient(135deg, #FF1493, #FF6EB4); }
        .stat-card-2 { background: linear-gradient(135deg, #7B2DD4, #A855F7); }
        .stat-card-3 { background: linear-gradient(135deg, #E0006A, #FF1493); }
        .stat-card-4 { background: linear-gradient(135deg, #4F46E5, #7B2DD4); }

        .stat-icon-wrap {
            width: 46px;
            height: 46px;
            background: rgba(255,255,255,0.2);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }
        .stat-value {
            font-size: 32px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 4px;
            position: relative;
            z-index: 1;
        }
        .stat-label {
            font-size: 12px;
            font-weight: 500;
            opacity: 0.85;
            position: relative;
            z-index: 1;
        }
        .stat-trend {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 10px;
            font-size: 11px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        .stat-trend .up { color: #A7F3D0; }
        .stat-trend .down { color: #FCA5A5; }
        .stat-trend span { opacity: 0.7; }

        /* Grid de 2 columnas */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 20px;
            margin-bottom: 28px;
        }

        /* Panel / Card */
        .panel {
            background: var(--card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .panel-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .panel-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
        }
        .panel-title-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(255,20,147,0.12), rgba(123,45,212,0.12));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--pink);
        }

        .panel-action {
            font-size: 12px;
            font-weight: 500;
            color: var(--pink);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: gap 0.2s;
        }
        .panel-action:hover { gap: 8px; }

        .panel-body { padding: 20px 24px; }

        /* Tabla de propiedades */
        .prop-table { width: 100%; border-collapse: collapse; }
        .prop-table th {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            padding: 0 0 14px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        .prop-table td {
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13.5px;
            vertical-align: middle;
        }
        .prop-table tr:last-child td { border-bottom: none; }
        .prop-table tr:hover td { background: rgba(255,20,147,0.02); }

        .prop-img {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(255,20,147,0.15), rgba(123,45,212,0.15));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pink);
            font-size: 18px;
        }

        .prop-name {
            font-weight: 600;
            color: var(--text);
            font-size: 13px;
        }
        .prop-location {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .prop-price {
            font-weight: 700;
            color: var(--purple);
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-venta    { background: rgba(255,20,147,0.1);  color: var(--pink); }
        .badge-renta    { background: rgba(123,45,212,0.1);  color: var(--purple); }
        .badge-vendida  { background: rgba(16,185,129,0.1);  color: #059669; }
        .badge-proceso  { background: rgba(245,158,11,0.1);  color: #D97706; }

        /* Acciones rápidas */
        .quick-actions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .quick-action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 20px 16px;
            border-radius: 14px;
            text-decoration: none;
            border: 1.5px solid var(--border);
            background: var(--bg);
            transition: all 0.25s;
            cursor: pointer;
        }
        .quick-action-btn:hover {
            border-color: var(--pink);
            background: linear-gradient(135deg, rgba(255,20,147,0.05), rgba(123,45,212,0.05));
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255,20,147,0.1);
        }

        .quick-action-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .qa-pink   .quick-action-icon { background: linear-gradient(135deg,#FF1493,#FF6EB4); color: white; }
        .qa-purple .quick-action-icon { background: linear-gradient(135deg,#7B2DD4,#A855F7); color: white; }
        .qa-green  .quick-action-icon { background: linear-gradient(135deg,#10B981,#34D399); color: white; }
        .qa-orange .quick-action-icon { background: linear-gradient(135deg,#F59E0B,#FCD34D); color: white; }

        .quick-action-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
            text-align: center;
        }

        /* Actividad reciente */
        .activity-list { display: flex; flex-direction: column; gap: 14px; }
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }
        .activity-dot {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .dot-pink   { background: rgba(255,20,147,0.12); color: var(--pink); }
        .dot-purple { background: rgba(123,45,212,0.12); color: var(--purple); }
        .dot-green  { background: rgba(16,185,129,0.12); color: #059669; }
        .dot-orange { background: rgba(245,158,11,0.12); color: #D97706; }

        .activity-text { flex: 1; }
        .activity-text strong { font-size: 13px; font-weight: 600; color: var(--text); }
        .activity-text p { font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        .activity-time { font-size: 11px; color: var(--text-muted); white-space: nowrap; }

        /* Bottom row */
        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }

        /* Mini stats */
        .mini-stat {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 0;
            border-bottom: 1px solid var(--border);
        }
        .mini-stat:last-child { border-bottom: none; }
        .mini-stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }
        .mini-stat-text { flex: 1; }
        .mini-stat-label { font-size: 11.5px; color: var(--text-muted); }
        .mini-stat-value { font-size: 18px; font-weight: 700; color: var(--text); }
        .mini-stat-value small { font-size: 12px; color: var(--text-muted); font-weight: 400; }

        /* Progress bar */
        .progress-wrap { margin-top: 4px; }
        .progress-bar {
            height: 5px;
            border-radius: 10px;
            background: var(--border);
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            border-radius: 10px;
            background: linear-gradient(90deg, var(--pink), var(--purple));
            transition: width 1s ease;
        }

        /* Responsive mobile */
        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .grid-2     { grid-template-columns: 1fr; }
            .grid-3     { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 768px) {
            :root { --sidebar-w: 0px; }
            .sidebar { transform: translateX(-265px); }
            .main-wrapper { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .welcome-banner { flex-direction: column; gap: 20px; }
            .grid-3 { grid-template-columns: 1fr; }
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(123,45,212,0.2); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,20,147,0.3); }
    </style>
</head>
<body>

    <!-- =========== SIDEBAR =========== -->
    <aside class="sidebar" id="sidebar">

        <!-- Brand -->
        <div class="sidebar-brand">
            <div class="brand-logo">
                <img src="/imagenes/inmobiliaria_bc.jpeg" alt="BC">
            </div>
            <div class="brand-info">
                <div class="brand-name"><span>Beatriz Campos</span></div>
                <div class="brand-sub">Inmobiliaria</div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">

            <div class="nav-section-label">Principal</div>

            <a href="#" class="nav-item active">
                <div class="nav-icon"><i class="fas fa-th-large"></i></div>
                Dashboard
            </a>

            <div class="nav-section-label" style="margin-top:14px;">Gestión</div>

            <a href="#" class="nav-item">
                <div class="nav-icon"><i class="fas fa-building"></i></div>
                Propiedades
                <span class="nav-badge">24</span>
            </a>

            <a href="#" class="nav-item">
                <div class="nav-icon"><i class="fas fa-users"></i></div>
                Clientes
                <span class="nav-badge">8</span>
            </a>

            <a href="#" class="nav-item">
                <div class="nav-icon"><i class="fas fa-file-contract"></i></div>
                Contratos
            </a>

            <a href="#" class="nav-item">
                <div class="nav-icon"><i class="fas fa-user-tie"></i></div>
                Agentes
            </a>

            <div class="nav-section-label" style="margin-top:14px;">Análisis</div>

            <a href="#" class="nav-item">
                <div class="nav-icon"><i class="fas fa-chart-bar"></i></div>
                Reportes
            </a>

            <a href="#" class="nav-item">
                <div class="nav-icon"><i class="fas fa-dollar-sign"></i></div>
                Finanzas
            </a>

            <div class="nav-section-label" style="margin-top:14px;">Sistema</div>

            <a href="#" class="nav-item">
                <div class="nav-icon"><i class="fas fa-bell"></i></div>
                Notificaciones
                <span class="nav-badge">3</span>
            </a>

            <a href="#" class="nav-item">
                <div class="nav-icon"><i class="fas fa-cog"></i></div>
                Configuración
            </a>

        </nav>

        <!-- Sidebar user -->
        <div class="sidebar-user">
            <div class="user-avatar-sm">BC</div>
            <div class="user-info-sm">
                <div class="user-name-sm">Administrador</div>
                <div class="user-role-sm">Super Admin</div>
            </div>
            <div class="user-actions">
                <a href="/"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>

    </aside>

    <!-- =========== MAIN =========== -->
    <div class="main-wrapper">

        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <div class="page-title">Dashboard</div>
                <div class="page-breadcrumb">
                    <a href="/" style="text-decoration:none;color:inherit;">Inicio</a>
                     › <span>Panel Administrativo</span>
                </div>
            </div>

            <div class="header-actions">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Buscar propiedades, clientes...">
                </div>

                <a href="#" class="header-btn" title="Agregar propiedad">
                    <i class="fas fa-plus"></i>
                </a>

                <a href="#" class="header-btn" title="Notificaciones">
                    <i class="fas fa-bell"></i>
                    <span class="notification-dot"></span>
                </a>

                <a href="#" class="header-btn" title="Mensajes">
                    <i class="fas fa-envelope"></i>
                </a>

                <div class="header-user">
                    <div class="header-avatar">BC</div>
                    <div class="header-user-info">
                        <div class="header-user-name">Administrador</div>
                        <div class="header-user-role">Super Admin</div>
                    </div>
                    <i class="fas fa-chevron-down header-user-arrow"></i>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="content">

            <!-- Banner de bienvenida -->
            <div class="welcome-banner">
                <div class="welcome-text">
                    <div class="welcome-greeting">
                        <i class="fas fa-sun" style="margin-right:6px;"></i> Buenos días, Administrador
                    </div>
                    <h2 class="welcome-heading">
                        Panel de Control<br><span>Beatriz Campos</span> Inmobiliaria
                    </h2>
                    <p class="welcome-desc">Gestiona tus propiedades, clientes y contratos desde un solo lugar.</p>
                </div>
                <div class="welcome-actions">
                    <a href="#" class="btn-primary">
                        <i class="fas fa-plus-circle"></i> Nueva Propiedad
                    </a>
                    <a href="#" class="btn-secondary">
                        <i class="fas fa-chart-line"></i> Ver Reportes
                    </a>
                </div>
            </div>

            <!-- Stats cards -->
            <div class="stats-grid">

                <div class="stat-card stat-card-1">
                    <div class="stat-icon-wrap"><i class="fas fa-building"></i></div>
                    <div class="stat-value">24</div>
                    <div class="stat-label">Propiedades Activas</div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up up"></i>
                        <span class="up">+4</span>
                        <span>este mes</span>
                    </div>
                </div>

                <div class="stat-card stat-card-2">
                    <div class="stat-icon-wrap"><i class="fas fa-users"></i></div>
                    <div class="stat-value">87</div>
                    <div class="stat-label">Clientes Registrados</div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up up"></i>
                        <span class="up">+12</span>
                        <span>este mes</span>
                    </div>
                </div>

                <div class="stat-card stat-card-3">
                    <div class="stat-icon-wrap"><i class="fas fa-file-signature"></i></div>
                    <div class="stat-value">13</div>
                    <div class="stat-label">Contratos Vigentes</div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up up"></i>
                        <span class="up">+2</span>
                        <span>este mes</span>
                    </div>
                </div>

                <div class="stat-card stat-card-4">
                    <div class="stat-icon-wrap"><i class="fas fa-dollar-sign"></i></div>
                    <div class="stat-value">$1.2M</div>
                    <div class="stat-label">Ventas del Mes</div>
                    <div class="stat-trend">
                        <i class="fas fa-arrow-up up"></i>
                        <span class="up">+18%</span>
                        <span>vs. mes anterior</span>
                    </div>
                </div>

            </div>

            <!-- Grid principal -->
            <div class="grid-2">

                <!-- Tabla de propiedades recientes -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <div class="panel-title-icon"><i class="fas fa-building"></i></div>
                            Propiedades Recientes
                        </div>
                        <a href="#" class="panel-action">
                            Ver todas <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="panel-body" style="padding:0 24px;">
                        <table class="prop-table">
                            <thead>
                                <tr>
                                    <th style="width:50px;"></th>
                                    <th>Propiedad</th>
                                    <th>Precio</th>
                                    <th>Estado</th>
                                    <th style="width:40px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="prop-img"><i class="fas fa-home"></i></div>
                                    </td>
                                    <td>
                                        <div class="prop-name">Casa en Col. Las Flores</div>
                                        <div class="prop-location"><i class="fas fa-map-marker-alt" style="font-size:10px;"></i> Guadalajara, JAL</div>
                                    </td>
                                    <td><span class="prop-price">$2,500,000</span></td>
                                    <td><span class="badge badge-venta">En Venta</span></td>
                                    <td>
                                        <a href="#" style="color:var(--text-muted);font-size:13px;"><i class="fas fa-ellipsis-v"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="prop-img" style="background:linear-gradient(135deg,rgba(123,45,212,0.15),rgba(168,85,247,0.15));color:var(--purple);">
                                            <i class="fas fa-building"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="prop-name">Departamento Centro</div>
                                        <div class="prop-location"><i class="fas fa-map-marker-alt" style="font-size:10px;"></i> Zapopan, JAL</div>
                                    </td>
                                    <td><span class="prop-price">$8,500/mes</span></td>
                                    <td><span class="badge badge-renta">En Renta</span></td>
                                    <td>
                                        <a href="#" style="color:var(--text-muted);font-size:13px;"><i class="fas fa-ellipsis-v"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="prop-img" style="background:rgba(16,185,129,0.12);color:#059669;">
                                            <i class="fas fa-home"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="prop-name">Casa Fracc. Jardines</div>
                                        <div class="prop-location"><i class="fas fa-map-marker-alt" style="font-size:10px;"></i> Tlaquepaque, JAL</div>
                                    </td>
                                    <td><span class="prop-price">$1,800,000</span></td>
                                    <td><span class="badge badge-vendida">Vendida</span></td>
                                    <td>
                                        <a href="#" style="color:var(--text-muted);font-size:13px;"><i class="fas fa-ellipsis-v"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="prop-img" style="background:rgba(245,158,11,0.12);color:#D97706;">
                                            <i class="fas fa-warehouse"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="prop-name">Local Comercial Av. López</div>
                                        <div class="prop-location"><i class="fas fa-map-marker-alt" style="font-size:10px;"></i> Guadalajara, JAL</div>
                                    </td>
                                    <td><span class="prop-price">$3,200,000</span></td>
                                    <td><span class="badge badge-proceso">En Proceso</span></td>
                                    <td>
                                        <a href="#" style="color:var(--text-muted);font-size:13px;"><i class="fas fa-ellipsis-v"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="prop-img"><i class="fas fa-home"></i></div>
                                    </td>
                                    <td>
                                        <div class="prop-name">Casa en Privada Robles</div>
                                        <div class="prop-location"><i class="fas fa-map-marker-alt" style="font-size:10px;"></i> Tonalá, JAL</div>
                                    </td>
                                    <td><span class="prop-price">$1,350,000</span></td>
                                    <td><span class="badge badge-venta">En Venta</span></td>
                                    <td>
                                        <a href="#" style="color:var(--text-muted);font-size:13px;"><i class="fas fa-ellipsis-v"></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Panel derecho -->
                <div style="display:flex;flex-direction:column;gap:20px;">

                    <!-- Acciones rápidas -->
                    <div class="panel">
                        <div class="panel-header">
                            <div class="panel-title">
                                <div class="panel-title-icon"><i class="fas fa-bolt"></i></div>
                                Acciones Rápidas
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="quick-actions-grid">
                                <a href="#" class="quick-action-btn qa-pink">
                                    <div class="quick-action-icon"><i class="fas fa-plus"></i></div>
                                    <div class="quick-action-label">Nueva Propiedad</div>
                                </a>
                                <a href="#" class="quick-action-btn qa-purple">
                                    <div class="quick-action-icon"><i class="fas fa-user-plus"></i></div>
                                    <div class="quick-action-label">Nuevo Cliente</div>
                                </a>
                                <a href="#" class="quick-action-btn qa-green">
                                    <div class="quick-action-icon"><i class="fas fa-file-contract"></i></div>
                                    <div class="quick-action-label">Nuevo Contrato</div>
                                </a>
                                <a href="#" class="quick-action-btn qa-orange">
                                    <div class="quick-action-icon"><i class="fas fa-chart-bar"></i></div>
                                    <div class="quick-action-label">Ver Reportes</div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Actividad reciente -->
                    <div class="panel">
                        <div class="panel-header">
                            <div class="panel-title">
                                <div class="panel-title-icon"><i class="fas fa-clock"></i></div>
                                Actividad Reciente
                            </div>
                            <a href="#" class="panel-action">Ver más <i class="fas fa-arrow-right"></i></a>
                        </div>
                        <div class="panel-body">
                            <div class="activity-list">
                                <div class="activity-item">
                                    <div class="activity-dot dot-pink"><i class="fas fa-home"></i></div>
                                    <div class="activity-text">
                                        <strong>Nueva propiedad agregada</strong>
                                        <p>Casa en Col. Las Flores, Guadalajara</p>
                                    </div>
                                    <div class="activity-time">Hace 2h</div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-dot dot-green"><i class="fas fa-handshake"></i></div>
                                    <div class="activity-text">
                                        <strong>Contrato firmado</strong>
                                        <p>Cliente: María González — Depto. Centro</p>
                                    </div>
                                    <div class="activity-time">Hace 5h</div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-dot dot-purple"><i class="fas fa-user-plus"></i></div>
                                    <div class="activity-text">
                                        <strong>Nuevo cliente registrado</strong>
                                        <p>Carlos Ramírez Pérez</p>
                                    </div>
                                    <div class="activity-time">Ayer</div>
                                </div>
                                <div class="activity-item">
                                    <div class="activity-dot dot-orange"><i class="fas fa-dollar-sign"></i></div>
                                    <div class="activity-text">
                                        <strong>Venta concluida</strong>
                                        <p>Casa Fracc. Jardines — $1,800,000</p>
                                    </div>
                                    <div class="activity-time">Ayer</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Fila inferior -->
            <div class="grid-3">

                <!-- Tipos de propiedad -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <div class="panel-title-icon"><i class="fas fa-chart-pie"></i></div>
                            Por Tipo
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="mini-stat">
                            <div class="mini-stat-icon" style="background:rgba(255,20,147,0.1);color:var(--pink);">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="mini-stat-text">
                                <div class="mini-stat-label">Casas</div>
                                <div class="mini-stat-value">14 <small>propiedades</small></div>
                                <div class="progress-wrap">
                                    <div class="progress-bar"><div class="progress-fill" style="width:58%;background:linear-gradient(90deg,var(--pink),var(--pink-light));"></div></div>
                                </div>
                            </div>
                        </div>
                        <div class="mini-stat">
                            <div class="mini-stat-icon" style="background:rgba(123,45,212,0.1);color:var(--purple);">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="mini-stat-text">
                                <div class="mini-stat-label">Departamentos</div>
                                <div class="mini-stat-value">7 <small>propiedades</small></div>
                                <div class="progress-wrap">
                                    <div class="progress-bar"><div class="progress-fill" style="width:29%;"></div></div>
                                </div>
                            </div>
                        </div>
                        <div class="mini-stat">
                            <div class="mini-stat-icon" style="background:rgba(245,158,11,0.1);color:#D97706;">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <div class="mini-stat-text">
                                <div class="mini-stat-label">Locales Comerciales</div>
                                <div class="mini-stat-value">3 <small>propiedades</small></div>
                                <div class="progress-wrap">
                                    <div class="progress-bar"><div class="progress-fill" style="width:12%;background:linear-gradient(90deg,#F59E0B,#FCD34D);"></div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clientes recientes -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <div class="panel-title-icon"><i class="fas fa-users"></i></div>
                            Clientes Recientes
                        </div>
                        <a href="#" class="panel-action">Ver todos <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="panel-body" style="padding-top:8px;">
                        <div style="display:flex;flex-direction:column;gap:12px;">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#FF1493,#FF69B4);display:flex;align-items:center;justify-content:center;color:white;font-size:14px;font-weight:700;flex-shrink:0;">M</div>
                                <div style="flex:1;">
                                    <div style="font-size:13px;font-weight:600;color:var(--text);">María González</div>
                                    <div style="font-size:11px;color:var(--text-muted);">Arrendatario</div>
                                </div>
                                <span class="badge badge-renta">Activo</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#7B2DD4,#A855F7);display:flex;align-items:center;justify-content:center;color:white;font-size:14px;font-weight:700;flex-shrink:0;">C</div>
                                <div style="flex:1;">
                                    <div style="font-size:13px;font-weight:600;color:var(--text);">Carlos Ramírez</div>
                                    <div style="font-size:11px;color:var(--text-muted);">Comprador</div>
                                </div>
                                <span class="badge badge-proceso">Proceso</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#10B981,#34D399);display:flex;align-items:center;justify-content:center;color:white;font-size:14px;font-weight:700;flex-shrink:0;">L</div>
                                <div style="flex:1;">
                                    <div style="font-size:13px;font-weight:600;color:var(--text);">Laura Martínez</div>
                                    <div style="font-size:11px;color:var(--text-muted);">Compradora</div>
                                </div>
                                <span class="badge badge-vendida">Finalizado</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#F59E0B,#FCD34D);display:flex;align-items:center;justify-content:center;color:white;font-size:14px;font-weight:700;flex-shrink:0;">R</div>
                                <div style="flex:1;">
                                    <div style="font-size:13px;font-weight:600;color:var(--text);">Roberto Sánchez</div>
                                    <div style="font-size:11px;color:var(--text-muted);">Interesado</div>
                                </div>
                                <span class="badge badge-venta">Nuevo</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Metas del mes -->
                <div class="panel">
                    <div class="panel-header">
                        <div class="panel-title">
                            <div class="panel-title-icon"><i class="fas fa-bullseye"></i></div>
                            Metas del Mes
                        </div>
                    </div>
                    <div class="panel-body">
                        <div style="display:flex;flex-direction:column;gap:18px;">
                            <div>
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                    <span style="font-size:13px;font-weight:500;color:var(--text);">Ventas</span>
                                    <span style="font-size:12px;font-weight:700;color:var(--pink);">75%</span>
                                </div>
                                <div class="progress-bar" style="height:8px;">
                                    <div class="progress-fill" style="width:75%;"></div>
                                </div>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">$1.2M de $1.6M</div>
                            </div>
                            <div>
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                    <span style="font-size:13px;font-weight:500;color:var(--text);">Nuevos Clientes</span>
                                    <span style="font-size:12px;font-weight:700;color:var(--purple);">60%</span>
                                </div>
                                <div class="progress-bar" style="height:8px;">
                                    <div class="progress-fill" style="width:60%;"></div>
                                </div>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">12 de 20 clientes</div>
                            </div>
                            <div>
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                    <span style="font-size:13px;font-weight:500;color:var(--text);">Propiedades Listadas</span>
                                    <span style="font-size:12px;font-weight:700;color:var(--pink);">80%</span>
                                </div>
                                <div class="progress-bar" style="height:8px;">
                                    <div class="progress-fill" style="width:80%;"></div>
                                </div>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">24 de 30 propiedades</div>
                            </div>
                            <div>
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                    <span style="font-size:13px;font-weight:500;color:var(--text);">Contratos Cerrados</span>
                                    <span style="font-size:12px;font-weight:700;color:var(--purple);">43%</span>
                                </div>
                                <div class="progress-bar" style="height:8px;">
                                    <div class="progress-fill" style="width:43%;"></div>
                                </div>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">13 de 30 contratos</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /grid-3 -->

        </main><!-- /content -->

        <!-- Footer -->
        <footer style="padding:18px 30px;border-top:1px solid var(--border);background:var(--card);display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:12px;color:var(--text-muted);">&copy; 2025 <strong style="color:var(--pink);">Beatriz Campos Inmobiliaria</strong>. Todos los derechos reservados.</span>
            <span style="font-size:11px;color:var(--text-muted);">Sistema v1.0 &nbsp;|&nbsp; <a href="/" style="color:var(--purple);text-decoration:none;">Ir al inicio</a></span>
        </footer>

    </div><!-- /main-wrapper -->

</body>
</html>
