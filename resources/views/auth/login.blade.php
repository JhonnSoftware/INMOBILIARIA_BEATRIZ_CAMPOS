<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrativo | BC Inmobiliaria</title>
    <link rel="icon" type="image/png" href="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
            background: #0f0c1d;
            display: grid;
            place-items: center;
            padding: 24px;
            overflow: hidden;
            position: relative;
        }

        /* ── Fondo animado ── */
        .bg-orbs {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: float 8s ease-in-out infinite;
        }
        .orb-1 { width: 500px; height: 500px; background: radial-gradient(circle, rgba(85,51,204,.45) 0%, transparent 70%); top: -120px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 400px; height: 400px; background: radial-gradient(circle, rgba(238,0,187,.35) 0%, transparent 70%); bottom: -100px; right: -80px; animation-delay: -3s; }
        .orb-3 { width: 320px; height: 320px; background: radial-gradient(circle, rgba(99,102,241,.3) 0%, transparent 70%); top: 50%; left: 50%; transform: translate(-50%,-50%); animation-delay: -5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }

        /* ── Shell ── */
        .login-shell {
            position: relative;
            z-index: 1;
            width: min(860px, 100%);
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,.55), 0 0 0 1px rgba(255,255,255,.06);
        }

        /* ── Panel izquierdo ── */
        .login-brand {
            padding: 38px 36px;
            background: linear-gradient(155deg, #12102a 0%, #1e1050 45%, #2d1b69 100%);
            color: #fff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .login-brand::before {
            content: '';
            position: absolute;
            top: -100px; right: -100px;
            width: 350px; height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(238,0,187,.2) 0%, transparent 65%);
        }
        .login-brand::after {
            content: '';
            position: absolute;
            bottom: -120px; left: -80px;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(85,51,204,.3) 0%, transparent 65%);
        }
        .brand-inner { position: relative; z-index: 1; }

        .brand-logo {
            width: 62px; height: 62px;
            border-radius: 16px;
            background: rgba(255,255,255,.08);
            border: 1.5px solid rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            backdrop-filter: blur(8px);
            margin-bottom: 18px;
        }
        .brand-logo img { width: 100%; height: 100%; object-fit: contain; padding: 8px; }

        .brand-title {
            font-size: 26px;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -.5px;
        }
        .brand-title .accent { color: #f472b6; }

        .brand-sub {
            margin-top: 10px;
            font-size: 12.5px;
            line-height: 1.7;
            color: rgba(255,255,255,.6);
            max-width: 300px;
        }

        .brand-divider {
            width: 40px; height: 3px;
            background: linear-gradient(90deg, #EE00BB, #5533CC);
            border-radius: 99px;
            margin: 18px 0;
        }

        .brand-list { display: grid; gap: 10px; }
        .brand-item {
            display: flex; align-items: center; gap: 10px;
            font-size: 12px; color: rgba(255,255,255,.75);
        }
        .brand-item-icon {
            width: 28px; height: 28px;
            border-radius: 8px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.1);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 11px;
        }
        .brand-item-icon.pink { color: #f472b6; }
        .brand-item-icon.violet { color: #a78bfa; }
        .brand-item-icon.blue { color: #60a5fa; }

        .brand-footer {
            position: relative; z-index: 1;
            font-size: 11px;
            color: rgba(255,255,255,.3);
        }

        /* ── Panel derecho ── */
        .login-panel {
            padding: 36px 38px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card { width: 100%; max-width: 340px; }

        /* Tag */
        .tag {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(238,0,187,.08), rgba(85,51,204,.08));
            border: 1px solid rgba(85,51,204,.15);
            color: #5533CC;
            font-size: 10px; font-weight: 800;
            letter-spacing: 1.2px; text-transform: uppercase;
        }
        .tag i { font-size: 9px; color: #EE00BB; }

        /* Título */
        .login-title {
            margin-top: 12px;
            font-size: 22px; font-weight: 900;
            color: #0f0c1d; line-height: 1.15;
        }
        .login-title .pink { color: #EE00BB; }

        .login-sub {
            margin-top: 7px;
            font-size: 12px; color: #64748b; line-height: 1.65;
        }
        .login-sub strong { color: #1a1a2e; font-weight: 700; }

        /* Separador */
        .form-sep {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
            margin: 16px 0;
        }

        /* Campos */
        .field { display: grid; gap: 6px; }
        .field label {
            font-size: 10.5px; font-weight: 800;
            letter-spacing: .7px; text-transform: uppercase;
            color: #374151;
        }
        .field-wrap { position: relative; }
        .field-icon {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%);
            font-size: 13px; color: #94a3b8;
            pointer-events: none;
            transition: .2s;
        }
        .field input {
            width: 100%;
            padding: 11px 12px 11px 38px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: #f8fafc;
            outline: none;
            font: 500 13px 'Poppins', sans-serif;
            color: #0f0c1d;
            transition: .2s;
        }
        .field input:focus {
            border-color: #EE00BB;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(238,0,187,.09);
        }
        .field input:focus + .field-icon,
        .field-wrap:focus-within .field-icon { color: #EE00BB; }

        /* Eye toggle */
        .eye-btn {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            color: #94a3b8; cursor: pointer;
            font-size: 13px; padding: 4px;
            transition: .2s;
        }
        .eye-btn:hover { color: #5533CC; }

        /* Formulario */
        .login-form { display: grid; gap: 12px; margin-top: 0; }

        /* Remember */
        .remember {
            display: flex; align-items: center; justify-content: space-between;
            gap: 10px; font-size: 11.5px; color: #64748b;
        }
        .remember-label {
            display: flex; align-items: center; gap: 7px;
            cursor: pointer; font-weight: 500;
        }
        .remember-label input { accent-color: #EE00BB; width: 14px; height: 14px; }
        .remember-note {
            font-size: 10.5px; color: #94a3b8;
            display: flex; align-items: center; gap: 5px;
        }
        .remember-note i { font-size: 9px; color: #EE00BB; }

        /* Alertas */
        .alert {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12px; line-height: 1.6;
            display: flex; align-items: flex-start; gap: 8px;
        }
        .alert i { margin-top: 1px; flex-shrink: 0; }
        .alert.error { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }
        .alert.success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; }

        /* Botón */
        .login-btn {
            display: flex; align-items: center; justify-content: center; gap: 9px;
            width: 100%; padding: 13px;
            border: none; border-radius: 12px;
            background: linear-gradient(135deg, #EE00BB 0%, #5533CC 100%);
            color: #fff;
            font: 800 13px 'Poppins', sans-serif;
            letter-spacing: .3px;
            cursor: pointer;
            position: relative; overflow: hidden;
            transition: .25s;
            box-shadow: 0 6px 20px rgba(238,0,187,.28), 0 3px 7px rgba(85,51,204,.18);
        }
        .login-btn::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.15) 0%, transparent 50%);
        }
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(238,0,187,.32), 0 5px 12px rgba(85,51,204,.22);
        }
        .login-btn:active { transform: translateY(0); }

        /* Helper */
        .helper-box {
            margin-top: 14px;
            padding: 11px 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 11.5px; color: #64748b; line-height: 1.7;
        }
        .helper-box strong { color: #1a1a2e; font-weight: 700; }
        .helper-box .badge {
            display: inline-block;
            padding: 1px 7px;
            background: rgba(85,51,204,.08);
            border-radius: 5px;
            color: #5533CC; font-weight: 700;
        }

        /* Volver */
        .back-link {
            display: inline-flex; align-items: center; gap: 7px;
            margin-top: 14px;
            color: #94a3b8; text-decoration: none;
            font-size: 11.5px; font-weight: 600;
            transition: .2s;
        }
        .back-link:hover { color: #5533CC; }
        .back-link i { font-size: 10px; }

        /* Responsive */
        @media(max-width: 720px) {
            .login-shell { grid-template-columns: 1fr; }
            .login-brand { display: none; }
            .login-panel { padding: 32px 22px; }
            .login-card { max-width: none; }
        }
    </style>
</head>
<body>

<div class="bg-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<div class="login-shell">

    {{-- ── Panel izquierdo: Marca ── --}}
    <section class="login-brand">
        <div class="brand-inner">
            <div class="brand-logo">
                <img src="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}" alt="BC Inmobiliaria">
            </div>

            <div class="brand-title">
                Acceso <span class="accent">Administrativo</span>
            </div>

            <p class="brand-sub">
                Ingresa con tu usuario o correo para gestionar el panel corporativo del sistema inmobiliario. Los usuarios inactivos y sin rol válido no pueden acceder.
            </p>

            <div class="brand-divider"></div>

            <div class="brand-list">
                <div class="brand-item">
                    <div class="brand-item-icon pink">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <span>Contraseñas con hash nativo de Laravel</span>
                </div>
                <div class="brand-item">
                    <div class="brand-item-icon violet">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <span>Roles y permisos por módulo configurados</span>
                </div>
                <div class="brand-item">
                    <div class="brand-item-icon blue">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span>Acceso al panel y proyectos desde una sola cuenta</span>
                </div>
            </div>
        </div>

        <div class="brand-footer">
            &copy; {{ date('Y') }} BC Inmobiliaria &mdash; Sistema de gestión
        </div>
    </section>

    {{-- ── Panel derecho: Formulario ── --}}
    <section class="login-panel">
        <div class="login-card">

            <span class="tag"><i class="fas fa-lock"></i> Acceso seguro</span>

            <h1 class="login-title">
                Inicia sesión en <span class="pink">BC&nbsp;Inmobiliaria</span>
            </h1>

            <p class="login-sub">
                Usa tu <strong>usuario</strong> o tu correo electrónico para entrar al panel administrativo.
            </p>

            @if(session('success'))
            <div class="alert success" style="margin-top:20px;">
                <i class="fas fa-circle-check"></i>
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert error" style="margin-top:20px;">
                <i class="fas fa-circle-exclamation"></i>
                {{ $errors->first() }}
            </div>
            @endif

            <div class="form-sep"></div>

            <form method="POST" action="{{ route('login.store') }}" class="login-form">
                @csrf

                <div class="field">
                    <label for="login">Usuario o correo</label>
                    <div class="field-wrap">
                        <input
                            type="text"
                            id="login"
                            name="login"
                            value="{{ old('login') }}"
                            maxlength="191"
                            placeholder="Ej: dueno o correo@bc.com"
                            required
                            autofocus
                        >
                        <i class="fas fa-user field-icon"></i>
                    </div>
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <div class="field-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Tu contraseña"
                            required
                        >
                        <i class="fas fa-key field-icon"></i>
                        <button type="button" class="eye-btn" onclick="togglePassword()" title="Mostrar contraseña">
                            <i class="fas fa-eye" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="remember">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        Recordarme
                    </label>
                    <span class="remember-note">
                        <i class="fas fa-circle-info"></i> Solo usuarios activos
                    </span>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-right-to-bracket"></i>
                    Ingresar al sistema
                </button>
            </form>

            <a href="{{ url('/') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver al sitio web
            </a>
        </div>
    </section>

</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
</body>
</html>
