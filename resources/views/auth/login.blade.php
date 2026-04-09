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
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
        body{min-height:100vh;font-family:'Poppins',sans-serif;background:linear-gradient(155deg,#1a1a2e 0%,#2d1b69 40%,#5533CC 75%,#EE00BB 100%);display:grid;place-items:center;padding:24px;}
        .login-shell{width:min(980px,100%);display:grid;grid-template-columns:1.1fr .95fr;border-radius:28px;overflow:hidden;box-shadow:0 30px 80px rgba(0,0,0,.28);background:#fff;}
        .login-brand{padding:52px 44px;background:linear-gradient(160deg,#1a1a2e 0%,#25144f 48%,#5533CC 100%);color:#fff;position:relative;overflow:hidden;}
        .login-brand::before{content:'';position:absolute;top:-70px;right:-70px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(238,0,187,.28) 0%,transparent 70%);}
        .login-brand::after{content:'';position:absolute;bottom:-80px;left:-60px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(85,51,204,.35) 0%,transparent 70%);}
        .brand-inner{position:relative;z-index:1;}
        .brand-logo{width:84px;height:84px;border-radius:24px;background:#fff;display:flex;align-items:center;justify-content:center;overflow:hidden;box-shadow:0 18px 40px rgba(0,0,0,.24);margin-bottom:24px;}
        .brand-logo img{width:100%;height:100%;object-fit:contain;padding:8px;}
        .brand-title{font-size:32px;font-weight:900;line-height:1.1;}
        .brand-title span{color:#ff9be8;}
        .brand-sub{margin-top:14px;font-size:14px;line-height:1.75;color:rgba(255,255,255,.76);max-width:420px;}
        .brand-list{display:grid;gap:10px;margin-top:28px;}
        .brand-item{display:flex;align-items:center;gap:10px;font-size:13px;color:rgba(255,255,255,.8);}
        .brand-item i{color:#ff9be8;}
        .login-panel{padding:48px 40px;display:flex;align-items:center;justify-content:center;}
        .login-card{width:100%;max-width:390px;}
        .tag{display:inline-flex;align-items:center;gap:8px;padding:6px 14px;border-radius:999px;background:rgba(238,0,187,.08);color:#5533CC;font-size:11px;font-weight:800;letter-spacing:1.4px;text-transform:uppercase;}
        h1{margin-top:16px;font-size:30px;font-weight:900;color:#1a1a2e;line-height:1.15;}
        h1 span{color:#EE00BB;}
        .sub{margin-top:10px;font-size:13px;color:#64748b;line-height:1.75;}
        form{display:grid;gap:16px;margin-top:28px;}
        .field label{display:block;margin-bottom:8px;font-size:12px;font-weight:800;letter-spacing:.7px;text-transform:uppercase;color:#1a1a2e;}
        .field-wrap{position:relative;}
        .field-wrap i{position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:14px;color:#94a3b8;}
        .field input{width:100%;padding:13px 14px 13px 42px;border:1.5px solid #e2e8f0;border-radius:14px;background:#f8fafc;outline:none;font:500 13px 'Poppins',sans-serif;color:#1a1a2e;transition:.2s;}
        .field input:focus{border-color:rgba(238,0,187,.35);background:#fff;box-shadow:0 0 0 4px rgba(238,0,187,.08);}
        .remember{display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:12px;color:#64748b;}
        .remember label{display:flex;align-items:center;gap:8px;cursor:pointer;}
        .remember input{accent-color:#EE00BB;}
        .alert{padding:12px 14px;border-radius:12px;font-size:12px;line-height:1.6;}
        .alert.error{background:#fff1f2;border:1px solid #fecdd3;color:#be123c;}
        .alert.success{background:#ecfdf5;border:1px solid #a7f3d0;color:#047857;}
        .login-btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;padding:14px;border:none;border-radius:14px;background:linear-gradient(135deg,#EE00BB,#5533CC);color:#fff;font:800 14px 'Poppins',sans-serif;cursor:pointer;box-shadow:0 14px 30px rgba(85,51,204,.22);transition:.2s;}
        .login-btn:hover{transform:translateY(-1px);}
        .helper{margin-top:16px;font-size:12px;color:#64748b;line-height:1.7;}
        .helper strong{color:#1a1a2e;}
        .back-link{display:inline-flex;align-items:center;gap:8px;margin-top:18px;color:#64748b;text-decoration:none;font-size:13px;font-weight:700;}
        @media(max-width:860px){.login-shell{grid-template-columns:1fr;}.login-brand{display:none;}.login-panel{padding:34px 22px;}.login-card{max-width:none;}}
    </style>
</head>
<body>
<div class="login-shell">
    <section class="login-brand">
        <div class="brand-inner">
            <div class="brand-logo"><img src="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}" alt="BC"></div>
            <div class="brand-title">Acceso <span>Administrativo</span></div>
            <p class="brand-sub">Ingresa con tu usuario o correo para gestionar el panel corporativo del sistema inmobiliario. Los usuarios inactivos y sin rol válido quedan fuera del módulo de administración.</p>
            <div class="brand-list">
                <div class="brand-item"><i class="fas fa-shield-halved"></i> Contraseñas con hash nativo de Laravel</div>
                <div class="brand-item"><i class="fas fa-users-cog"></i> Base lista para roles y permisos por módulo</div>
                <div class="brand-item"><i class="fas fa-chart-line"></i> Acceso al panel principal y proyectos desde una sola cuenta</div>
            </div>
        </div>
    </section>

    <section class="login-panel">
        <div class="login-card">
            <span class="tag"><i class="fas fa-lock"></i> Acceso seguro</span>
            <h1>Inicia sesión en <span>BC Inmobiliaria</span></h1>
            <p class="sub">Usa tu <strong>username</strong> o tu correo electrónico para entrar al panel administrativo.</p>

            @if(session('success'))
            <div class="alert success" style="margin-top:18px;">{{ session('success') }}</div>
            @endif

            @if($errors->any())
            <div class="alert error" style="margin-top:18px;">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="field">
                    <label for="login">Usuario o correo</label>
                    <div class="field-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" id="login" name="login" value="{{ old('login') }}" maxlength="191" required autofocus>
                    </div>
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <div class="field-wrap">
                        <i class="fas fa-key"></i>
                        <input type="password" id="password" name="password" required>
                    </div>
                </div>

                <div class="remember">
                    <label><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Recordarme</label>
                    <span>Solo para usuarios activos</span>
                </div>

                <button type="submit" class="login-btn"><i class="fas fa-sign-in-alt"></i> Ingresar al sistema</button>
            </form>

            <div class="helper">
                Usuario de desarrollo sembrado por defecto: <strong>test@example.com</strong> o <strong>dueno</strong>.<br>
                Contraseña local: <strong>password</strong>. Cámbiala apenas ingreses al sistema.
            </div>

            <a href="{{ url('/') }}" class="back-link"><i class="fas fa-arrow-left"></i> Volver al sitio web</a>
        </div>
    </section>
</div>
</body>
</html>
