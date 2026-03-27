<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso — Beatriz Campos Inmobiliaria</title>
    <link rel="icon" type="image/png" href="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
        html,body{height:100%;}
        body{
            font-family:'Poppins',sans-serif;
            display:grid;
            grid-template-columns:1fr 1fr;
            min-height:100vh;
        }

        /* ===== LADO IZQUIERDO — branding ===== */
        .left{
            background:linear-gradient(155deg,#1a1a2e 0%,#2d1b69 40%,#5533CC 75%,#EE00BB 100%);
            display:flex;flex-direction:column;
            align-items:center;justify-content:center;
            padding:48px 52px;
            position:relative;overflow:hidden;
        }
        /* destellos decorativos */
        .left::before{
            content:'';position:absolute;top:-120px;right:-120px;
            width:380px;height:380px;border-radius:50%;
            background:radial-gradient(circle,rgba(238,0,187,.25) 0%,transparent 70%);
        }
        .left::after{
            content:'';position:absolute;bottom:-100px;left:-80px;
            width:320px;height:320px;border-radius:50%;
            background:radial-gradient(circle,rgba(85,51,204,.3) 0%,transparent 70%);
        }
        .left-content{position:relative;z-index:1;text-align:center;}

        /* logo */
        .brand-logo-wrap{
            width:140px;height:140px;border-radius:32px;
            background:#ffffff;
            border:2px solid rgba(255,255,255,.2);
            display:flex;align-items:center;justify-content:center;
            margin:0 auto 28px;
            box-shadow:0 20px 60px rgba(0,0,0,.3);
            overflow:hidden;
        }
        .brand-logo-wrap img{width:100%;height:100%;object-fit:contain;padding:12px;}

        .brand-name{
            font-size:26px;font-weight:900;color:#fff;
            line-height:1.15;margin-bottom:6px;
        }
        .brand-name em{
            font-style:normal;
            background:linear-gradient(90deg,#ffaaee,#fff);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
        }
        .brand-sub{
            font-size:11px;font-weight:600;letter-spacing:3px;
            text-transform:uppercase;color:rgba(255,255,255,.55);
            margin-bottom:40px;
        }

        /* stats pequeños */
        .brand-stats{
            display:flex;gap:28px;justify-content:center;
            border-top:1px solid rgba(255,255,255,.12);
            padding-top:32px;margin-top:8px;
        }
        .bs-item{text-align:center;}
        .bs-num{font-size:24px;font-weight:900;color:#EE00BB;line-height:1;}
        .bs-lbl{font-size:10px;color:rgba(255,255,255,.5);margin-top:3px;font-weight:400;}

        /* chips decorativos */
        .brand-chips{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin-bottom:32px;}
        .chip{
            display:flex;align-items:center;gap:6px;
            background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);
            border-radius:50px;padding:6px 14px;
            color:rgba(255,255,255,.8);font-size:11px;font-weight:500;
        }
        .chip i{font-size:11px;color:#EE00BB;}

        /* volver */
        .back-link{
            display:inline-flex;align-items:center;gap:7px;
            color:rgba(255,255,255,.45);font-size:12px;font-weight:500;
            text-decoration:none;transition:.2s;margin-top:32px;
            position:relative;z-index:1;
        }
        .back-link:hover{color:rgba(255,255,255,.85);}

        /* ===== LADO DERECHO — acceso ===== */
        .right{
            background:#fff;
            display:flex;flex-direction:column;
            align-items:center;justify-content:center;
            padding:48px 72px;
            position:relative;
        }
        .right::before{
            content:'';position:absolute;top:0;left:0;right:0;height:4px;
            background:linear-gradient(90deg,#EE00BB,#5533CC,#EE00BB);
        }
        .right-inner{width:100%;max-width:480px;}

        .right-tag{
            display:inline-flex;align-items:center;gap:7px;
            background:linear-gradient(135deg,rgba(238,0,187,.08),rgba(85,51,204,.08));
            color:#5533CC;border-radius:50px;padding:5px 16px;
            font-size:11px;font-weight:700;letter-spacing:1.5px;
            text-transform:uppercase;margin-bottom:16px;
        }
        .right-h1{
            font-size:30px;font-weight:800;color:#1a1a2e;
            line-height:1.2;margin-bottom:8px;
        }
        .right-h1 span{color:#EE00BB;}
        .right-sub{font-size:13.5px;color:#64748b;line-height:1.7;margin-bottom:36px;}

        /* tabs rol */
        .role-tabs{display:flex;gap:0;background:#f1f5f9;border-radius:14px;padding:5px;margin-bottom:28px;}
        .role-tab{
            flex:1;display:flex;align-items:center;justify-content:center;gap:8px;
            padding:10px 14px;border-radius:10px;cursor:pointer;
            font-size:13px;font-weight:600;color:#64748b;
            transition:.25s;border:none;background:none;font-family:'Poppins',sans-serif;
        }
        .role-tab.active-cliente{background:#fff;color:#EE00BB;box-shadow:0 2px 10px rgba(0,0,0,.08);}
        .role-tab.active-admin{background:#fff;color:#5533CC;box-shadow:0 2px 10px rgba(0,0,0,.08);}
        .role-tab i{font-size:14px;}

        /* form */
        .login-form{display:flex;flex-direction:column;gap:16px;margin-bottom:20px;}
        .form-group{display:flex;flex-direction:column;gap:6px;}
        .form-label{font-size:12.5px;font-weight:600;color:#374151;}
        .form-input-wrap{position:relative;}
        .form-input-wrap i.fi-icon{
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            font-size:14px;color:#94a3b8;
        }
        .form-input{
            width:100%;padding:12px 14px 12px 42px;
            border:1.5px solid #e2e8f0;border-radius:12px;
            font-family:'Poppins',sans-serif;font-size:13.5px;color:#1a1a2e;
            background:#fafbfc;outline:none;transition:.2s;
        }
        .form-input:focus{border-color:#EE00BB;background:#fff;box-shadow:0 0 0 3px rgba(238,0,187,.07);}
        .form-input.focus-admin:focus{border-color:#5533CC;box-shadow:0 0 0 3px rgba(85,51,204,.07);}

        .form-input-wrap .eye-btn{
            position:absolute;right:14px;top:50%;transform:translateY(-50%);
            background:none;border:none;cursor:pointer;color:#94a3b8;font-size:14px;
            transition:.2s;padding:0;
        }
        .form-input-wrap .eye-btn:hover{color:#64748b;}

        .form-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;}
        .remember-wrap{display:flex;align-items:center;gap:7px;cursor:pointer;}
        .remember-wrap input[type=checkbox]{
            width:15px;height:15px;accent-color:#EE00BB;cursor:pointer;
        }
        .remember-wrap span{font-size:12px;color:#64748b;}
        .forgot-link{font-size:12px;color:#EE00BB;text-decoration:none;font-weight:600;transition:.2s;}
        .forgot-link:hover{color:#C4009A;}

        /* btn login */
        .login-btn{
            width:100%;padding:14px;border-radius:14px;border:none;
            font-family:'Poppins',sans-serif;font-size:14px;font-weight:700;
            color:#fff;cursor:pointer;transition:.3s;
            display:flex;align-items:center;justify-content:center;gap:9px;
            background:linear-gradient(135deg,#EE00BB,#C4009A);
            box-shadow:0 8px 24px rgba(238,0,187,.35);
        }
        .login-btn.btn-admin{
            background:linear-gradient(135deg,#5533CC,#3D1F99);
            box-shadow:0 8px 24px rgba(85,51,204,.35);
        }
        .login-btn:hover{transform:translateY(-2px);filter:brightness(1.08);}
        .login-btn:active{transform:translateY(0);}

        /* error */
        .login-error{
            display:none;
            background:#fff0f5;border:1.5px solid #fca5a5;
            border-radius:10px;padding:10px 14px;
            font-size:12.5px;color:#dc2626;
            align-items:center;gap:8px;margin-top:-6px;
        }
        .login-error.show{display:flex;}

        /* divider */
        .or-divider{
            display:flex;align-items:center;gap:12px;
            margin-bottom:20px;
        }
        .or-line{flex:1;height:1px;background:#f1f5f9;}
        .or-text{font-size:11px;color:#cbd5e1;font-weight:500;white-space:nowrap;}

        /* WA button */
        .wa-btn{
            display:flex;align-items:center;justify-content:center;gap:10px;
            width:100%;padding:14px;border-radius:14px;
            background:#f0fdf4;border:1.5px solid #bbf7d0;
            color:#16a34a;font-size:14px;font-weight:600;
            text-decoration:none;transition:.3s;
        }
        .wa-btn:hover{background:#dcfce7;border-color:#86efac;transform:translateY(-2px);}
        .wa-btn i{font-size:18px;}

        /* footer derecha */
        .back-home-btn{
            display:inline-flex;align-items:center;gap:8px;
            color:#64748b;font-size:13px;font-weight:500;
            text-decoration:none;margin-bottom:28px;
            padding:9px 18px;border-radius:10px;
            border:1px solid #e2e8f0;background:#f8fafc;
            transition:.2s;
        }
        .back-home-btn:hover{background:#f0f2ff;color:#5533CC;border-color:rgba(85,51,204,.25);}
        .back-home-btn i{font-size:12px;}
        .right-footer{
            text-align:center;margin-top:28px;
            padding-top:20px;border-top:1px solid #f1f5f9;
        }
        .right-footer p{font-size:11px;color:#cbd5e1;}
        .right-footer strong{color:#EE00BB;}

        /* ===== RESPONSIVE ===== */
        @media(max-width:1024px){
            .right{padding:48px 40px;}
            .right-inner{max-width:440px;}
        }
        @media(max-width:900px){
            body{grid-template-columns:1fr;}
            .left{display:none;}
            .right{padding:40px 32px;min-height:100vh;}
            .right-inner{max-width:100%;}
            .right::before{height:3px;}
        }
        @media(max-width:480px){
            .right{padding:32px 20px 36px;}
            .right-h1{font-size:24px;}
            .access-card{padding:16px 18px;gap:14px;}
            .ac-icon{width:44px;height:44px;font-size:18px;}
            .ac-text .ac-title{font-size:14px;}
            .right-inner{max-width:100%;}
        }
    </style>
</head>
<body>

    <!-- IZQUIERDA: branding -->
    <div class="left">
        <div class="left-content">
            <div class="brand-logo-wrap">
                <img src="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}" alt="Beatriz Campos Inmobiliaria">
            </div>
            <div class="brand-name">
                <em>Beatriz Campos</em><br>Inmobiliaria
            </div>
            <div class="brand-sub">Sistema de Gestión</div>

            <div class="brand-chips">
                <div class="chip"><i class="fas fa-check-circle"></i> Proyectos 100% ejecutados</div>
                <div class="chip"><i class="fas fa-hand-holding-usd"></i> Financiamiento sin intereses</div>
                <div class="chip"><i class="fas fa-city"></i> Habilitación Urbana</div>
                <div class="chip"><i class="fas fa-map-marker-alt"></i> Hualhuas, Junín</div>
            </div>

            <div class="brand-stats">
                <div class="bs-item">
                    <div class="bs-num">3+</div>
                    <div class="bs-lbl">Proyectos</div>
                </div>
                <div class="bs-item">
                    <div class="bs-num">252+</div>
                    <div class="bs-lbl">Familias</div>
                </div>
                <div class="bs-item">
                    <div class="bs-num">0%</div>
                    <div class="bs-lbl">Intereses</div>
                </div>
            </div>

            <a href="{{ url('/') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver al sitio web
            </a>
        </div>
    </div>

    <!-- DERECHA: acceso -->
    <div class="right">
        <div class="right-inner">

            <div class="right-tag">
                <i class="fas fa-lock"></i> Acceso Seguro
            </div>

            <h1 class="right-h1">
                Bienvenido de<br><span>vuelta</span>
            </h1>
            <p class="right-sub">
                Ingresa tus credenciales para acceder al sistema de gestión inmobiliaria.
            </p>

            <!-- Tabs rol -->
            <div class="role-tabs">
                <button class="role-tab active-cliente" id="tabCliente" onclick="setRol('cliente')">
                    <i class="fas fa-user"></i> Cliente
                </button>
                <button class="role-tab" id="tabAdmin" onclick="setRol('admin')">
                    <i class="fas fa-shield-alt"></i> Administrador
                </button>
            </div>

            <!-- Formulario -->
            <form class="login-form" id="loginForm" onsubmit="handleLogin(event)">

                <div class="form-group">
                    <label class="form-label">Usuario</label>
                    <div class="form-input-wrap">
                        <i class="fas fa-user fi-icon"></i>
                        <input type="text" id="inputUser" class="form-input" placeholder="Ingresa tu usuario">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <div class="form-input-wrap">
                        <i class="fas fa-lock fi-icon"></i>
                        <input type="password" id="inputPass" class="form-input" placeholder="••••••••">
                        <button type="button" class="eye-btn" onclick="togglePass()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="login-error" id="loginError">
                    <i class="fas fa-exclamation-circle"></i>
                    <span id="errorMsg">Usuario o contraseña incorrectos.</span>
                </div>

                <div class="form-row">
                    <label class="remember-wrap">
                        <input type="checkbox"> <span>Recordarme</span>
                    </label>
                    <a href="#" class="forgot-link">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="login-btn" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                </button>

            </form>


            <div class="right-footer">
                <a href="{{ url('/') }}" class="back-home-btn" style="display:inline-flex;margin-bottom:16px;">
                    <i class="fas fa-arrow-left"></i> Volver a la página principal
                </a>
                <p>&copy; 2026 <strong>Beatriz Campos Inmobiliaria</strong>. Todos los derechos reservados.</p>
            </div>

        </div>
    </div>

<script>
    let rolActual = 'cliente';

    function setRol(rol) {
        rolActual = rol;
        const tabC = document.getElementById('tabCliente');
        const tabA = document.getElementById('tabAdmin');
        const btn  = document.getElementById('loginBtn');
        const inputs = document.querySelectorAll('.form-input');

        tabC.className = 'role-tab' + (rol === 'cliente' ? ' active-cliente' : '');
        tabA.className = 'role-tab' + (rol === 'admin'   ? ' active-admin'   : '');

        if (rol === 'admin') {
            btn.className = 'login-btn btn-admin';
            inputs.forEach(i => i.classList.add('focus-admin'));
        } else {
            btn.className = 'login-btn';
            inputs.forEach(i => i.classList.remove('focus-admin'));
        }
        document.getElementById('loginError').classList.remove('show');
    }

    function togglePass() {
        const input = document.getElementById('inputPass');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }

    function handleLogin(e) {
        e.preventDefault();
        const user = document.getElementById('inputUser').value.trim();
        const pass = document.getElementById('inputPass').value.trim();
        const err  = document.getElementById('loginError');
        const msg  = document.getElementById('errorMsg');

        if (!user || !pass) {
            msg.textContent = 'Por favor completa todos los campos.';
            err.classList.add('show'); return;
        }

        if (rolActual === 'admin') {
            if (user === 'admin' && pass === 'admin123') {
                window.location.href = '{{ url("/admin") }}';
            } else {
                msg.textContent = 'Credenciales de administrador incorrectas.';
                err.classList.add('show');
            }
        } else {
            if (user && pass) {
                window.location.href = '{{ url("/cliente") }}';
            } else {
                msg.textContent = 'Usuario o contraseña incorrectos.';
                err.classList.add('show');
            }
        }
    }
</script>
</body>
</html>
