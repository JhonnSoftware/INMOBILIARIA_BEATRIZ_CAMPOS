<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Beatriz Campos Inmobiliaria')</title>
    <link rel="icon" type="image/png" href="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
        html{scroll-behavior:smooth;}
        body{font-family:'Poppins',sans-serif;color:#1a1a2e;overflow-x:hidden;}
        :root{
            --mg:#EE00BB;--mg2:#C4009A;--vt:#5533CC;--vt2:#3D1F99;
            --dark:#1a1a2e;--dark2:#16213e;--gray:#64748b;--light:#f8f9ff;--white:#ffffff;
        }
        /* NAVBAR */
        .navbar{position:fixed;top:0;left:0;right:0;z-index:1000;background:rgba(255,255,255,0.97);backdrop-filter:blur(16px);border-bottom:1px solid rgba(85,51,204,0.08);transition:box-shadow .3s;}
        .navbar.shadow{box-shadow:0 4px 32px rgba(85,51,204,0.12);}
        .nav-wrap{max-width:1260px;margin:0 auto;padding:0 28px;height:70px;display:flex;align-items:center;justify-content:space-between;gap:20px;}
        .nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
        .nav-brand img{height:44px;width:44px;object-fit:contain;border-radius:8px;}
        .nav-brand-text{display:flex;flex-direction:column;line-height:1.1;}
        .nav-brand-text b{font-size:14px;font-weight:800;color:var(--vt);}
        .nav-brand-text span{font-size:9px;font-weight:600;letter-spacing:2.5px;color:var(--mg);text-transform:uppercase;}
        .nav-menu{display:flex;gap:2px;list-style:none;align-items:center;}
        .nav-menu a{padding:7px 14px;border-radius:8px;font-size:13px;font-weight:500;color:var(--dark);text-decoration:none;transition:all .2s;}
        .nav-menu a:hover,.nav-menu a.active{background:rgba(85,51,204,0.06);color:var(--vt);}
        .nav-wa{display:flex;align-items:center;gap:8px;background:#25D366;color:#fff;padding:9px 20px;border-radius:50px;font-size:13px;font-weight:700;text-decoration:none;transition:all .3s;box-shadow:0 4px 16px rgba(37,211,102,.3);white-space:nowrap;}
        .nav-wa:hover{background:#1db954;transform:translateY(-2px);}
        .nav-login{display:flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--vt),var(--vt2));color:#fff;padding:9px 20px;border-radius:50px;font-size:13px;font-weight:700;text-decoration:none;transition:all .3s;box-shadow:0 4px 16px rgba(85,51,204,.3);white-space:nowrap;}
        .nav-login:hover{transform:translateY(-2px);}
        .hamburger{display:none;border:none;background:none;font-size:22px;color:var(--dark);cursor:pointer;}
        .mob-nav{display:none;flex-direction:column;gap:4px;position:fixed;top:70px;left:0;right:0;background:#fff;padding:16px 24px 24px;box-shadow:0 20px 40px rgba(0,0,0,.08);z-index:999;}
        .mob-nav.open{display:flex;}
        .mob-nav a:not(.nav-wa):not(.nav-login){padding:11px 14px;border-radius:10px;font-size:14px;color:var(--dark);text-decoration:none;font-weight:500;transition:.2s;}
        .mob-nav a:not(.nav-wa):not(.nav-login):hover{background:var(--light);color:var(--vt);}
        .mob-nav .nav-wa{margin-top:8px;justify-content:center;}
        .mob-nav .nav-login{margin-top:4px;justify-content:center;}
        /* FOOTER */
        footer{background:#0c0c1a;padding:56px 28px 28px;}
        .footer-wrap{max-width:1260px;margin:0 auto;}
        .footer-top{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px;padding-bottom:36px;border-bottom:1px solid rgba(255,255,255,.06);margin-bottom:24px;}
        .f-brand a{display:flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:14px;}
        .f-brand a img{height:42px;width:42px;object-fit:contain;border-radius:8px;}
        .f-brand a span{font-size:15px;font-weight:800;color:#fff;}
        .f-brand p{font-size:12.5px;color:rgba(255,255,255,.45);line-height:1.8;max-width:260px;}
        .f-social{display:flex;gap:8px;margin-top:16px;}
        .fs-btn{width:36px;height:36px;border-radius:9px;background:rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.5);text-decoration:none;font-size:14px;transition:.3s;}
        .fs-btn:hover{background:var(--mg);color:#fff;}
        .f-col h4{font-size:13px;font-weight:700;color:#fff;margin-bottom:14px;}
        .f-links{list-style:none;display:flex;flex-direction:column;gap:9px;}
        .f-links a{font-size:12.5px;color:rgba(255,255,255,.45);text-decoration:none;transition:.2s;}
        .f-links a:hover{color:rgba(238,0,187,.8);}
        .footer-bottom{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
        .footer-bottom p{font-size:12px;color:rgba(255,255,255,.35);}
        .footer-bottom strong{color:var(--mg);}
        .f-ver{background:rgba(238,0,187,.15);color:rgba(238,0,187,.8);padding:4px 14px;border-radius:50px;font-size:11px;font-weight:600;}
        /* FLOAT */
        .float-social{position:fixed;right:20px;bottom:80px;z-index:500;display:flex;flex-direction:column;gap:12px;align-items:center;}
        .fs-ball{width:50px;height:50px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;text-decoration:none;box-shadow:0 6px 20px rgba(0,0,0,.25);transition:.3s;position:relative;}
        .fs-ball:hover{transform:scale(1.15);}
        .fs-ball.wa{background:linear-gradient(135deg,#25D366,#1da851);}
        .fs-ball.yt{background:linear-gradient(135deg,#ff0000,#cc0000);}
        .fs-ball.ig{background:radial-gradient(circle at 30% 107%,#fdf497 0%,#fdf497 5%,#fd5949 45%,#d6249f 60%,#285AEB 90%);}
        .fs-ball.tt{background:#000;}
        .fs-ball.fb{background:linear-gradient(135deg,#1877F2,#0d5fc7);}
        .fs-ball .fs-tooltip{position:absolute;right:58px;background:rgba(0,0,0,.75);color:#fff;font-size:11px;font-weight:600;white-space:nowrap;padding:4px 10px;border-radius:6px;opacity:0;pointer-events:none;transition:.2s;font-family:'Poppins',sans-serif;}
        .fs-ball:hover .fs-tooltip{opacity:1;}
        .back-top{position:fixed;bottom:28px;right:26px;z-index:500;width:46px;height:46px;border-radius:13px;background:linear-gradient(135deg,var(--mg),var(--vt));display:flex;align-items:center;justify-content:center;color:#fff;font-size:17px;text-decoration:none;box-shadow:0 8px 24px rgba(238,0,187,.4);opacity:0;pointer-events:none;transition:.3s;}
        .back-top.show{opacity:1;pointer-events:all;}
        /* RESPONSIVE */
        @media(max-width:768px){
            .nav-menu,.nav-wa,.nav-login{display:none;}
            .hamburger{display:block;}
            .footer-top{grid-template-columns:1fr;}
        }
        @yield('head')
    </style>
</head>
<body>
<nav class="navbar" id="navbar">
    <div class="nav-wrap">
        <a href="{{ url('/') }}" class="nav-brand">
            <img src="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}" alt="Logo Beatriz Campos">
            <div class="nav-brand-text">
                <b>Beatriz Campos</b>
                <span>Inmobiliaria</span>
            </div>
        </a>
        <ul class="nav-menu">
            <li><a href="{{ url('/') }}#inicio">Inicio</a></li>
            <li><a href="{{ url('/') }}#proyectos" class="active">Proyectos</a></li>
            <li><a href="{{ url('/') }}#por-que">Nosotros</a></li>
            <li><a href="{{ url('/') }}#galeria">Galería</a></li>
            <li><a href="{{ url('/') }}#ubicacion">Ubicación</a></li>
            <li><a href="{{ url('/') }}#cta">Contacto</a></li>
        </ul>
        <a href="https://wa.me/51929303999" class="nav-wa" target="_blank">
            <i class="fab fa-whatsapp"></i> WhatsApp
        </a>
        <a href="{{ url('/acceso') }}" class="nav-login">
            <i class="fas fa-sign-in-alt"></i> Ingresar
        </a>
        <button class="hamburger" id="ham" aria-label="Menú">
            <i class="fas fa-bars" id="hamIcon"></i>
        </button>
    </div>
</nav>
<div class="mob-nav" id="mobNav">
    <a href="{{ url('/') }}#inicio" onclick="closeMob()">Inicio</a>
    <a href="{{ url('/') }}#proyectos" onclick="closeMob()">Proyectos</a>
    <a href="{{ url('/') }}#por-que" onclick="closeMob()">Nosotros</a>
    <a href="{{ url('/') }}#galeria" onclick="closeMob()">Galería</a>
    <a href="{{ url('/') }}#ubicacion" onclick="closeMob()">Ubicación</a>
    <a href="{{ url('/') }}#cta" onclick="closeMob()">Contacto</a>
    <a href="https://wa.me/51929303999" class="nav-wa" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
    <a href="{{ url('/acceso') }}" class="nav-login" style="margin-top:4px;justify-content:center;"><i class="fas fa-sign-in-alt"></i> Ingresar</a>
</div>

@yield('content')

<footer>
    <div class="footer-wrap">
        <div class="footer-top">
            <div class="f-brand">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('imagenes/imagenes_dashboard/logo_02.png') }}" alt="Logo BC Inmobiliaria">
                    <span>Beatriz Campos Inmobiliaria</span>
                </a>
                <p>Hacemos realidad el sueño de miles de familias en Junín. Lotes con habilitación urbana completa y financiamiento sin intereses.</p>
                <div class="f-social">
                    <a href="https://www.facebook.com/inmobiliariahualhuas" class="fs-btn" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/inmobiliaria.beatriz.campos" class="fs-btn" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.tiktok.com/@inmobiliariabeatrizcampo" class="fs-btn" target="_blank"><i class="fab fa-tiktok"></i></a>
                    <a href="https://wa.me/51929303999" class="fs-btn" target="_blank"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="f-col">
                <h4>Proyectos</h4>
                <ul class="f-links">
                    <li><a href="{{ url('/proyectos/residencial-aurora') }}">Residencial Aurora</a></li>
                    <li><a href="{{ url('/proyectos/residencial-la-colina') }}">Residencial La Colina</a></li>
                    <li><a href="{{ url('/proyectos/residencial-mi-hogar') }}">Residencial Mi Hogar</a></li>
                    <li><a href="{{ url('/proyectos/residencial-san-ignacio') }}">Residencial San Ignacio</a></li>
                    <li><a href="{{ url('/proyectos/residencial-victor-campos') }}">Residencial Victor Campos</a></li>
                </ul>
            </div>
            <div class="f-col">
                <h4>Empresa</h4>
                <ul class="f-links">
                    <li><a href="{{ url('/') }}#por-que">Por qué elegirnos</a></li>
                    <li><a href="{{ url('/') }}#galeria">Galería de obras</a></li>
                    <li><a href="{{ url('/') }}#testimonio">Testimonios</a></li>
                    <li><a href="{{ url('/') }}#pasos">Cómo comprar</a></li>
                </ul>
            </div>
            <div class="f-col">
                <h4>Contacto</h4>
                <ul class="f-links">
                    <li><a href="#">Jr. 28 de Julio N° 495</a></li>
                    <li><a href="#">Huancayo, Junín, Perú</a></li>
                    <li><a href="tel:+51929303999">+51 929 303 999</a></li>
                    <li><a href="https://wa.me/51929303999">WhatsApp</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 <strong>Beatriz Campos Inmobiliaria</strong>. Todos los derechos reservados.</p>
            <span class="f-ver">BC INMOBILIARIA v1.0</span>
        </div>
    </div>
</footer>

<div class="float-social">
    <a href="https://www.facebook.com/inmobiliariahualhuas" target="_blank" class="fs-ball fb"><i class="fab fa-facebook-f"></i><span class="fs-tooltip">Facebook</span></a>
    <a href="https://wa.me/51929303999" target="_blank" class="fs-ball wa"><i class="fab fa-whatsapp"></i><span class="fs-tooltip">WhatsApp</span></a>
    <a href="https://www.youtube.com/@inmobiliariabeatrizcampos" target="_blank" class="fs-ball yt"><i class="fab fa-youtube"></i><span class="fs-tooltip">YouTube</span></a>
    <a href="https://www.instagram.com/inmobiliaria.beatriz.campos" target="_blank" class="fs-ball ig"><i class="fab fa-instagram"></i><span class="fs-tooltip">Instagram</span></a>
    <a href="https://www.tiktok.com/@inmobiliariabeatrizcampo" target="_blank" class="fs-ball tt"><i class="fab fa-tiktok"></i><span class="fs-tooltip">TikTok</span></a>
</div>
<a href="#top" class="back-top" id="backTop"><i class="fas fa-chevron-up"></i></a>

<script>
    const nav = document.getElementById('navbar');
    const bt  = document.getElementById('backTop');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('shadow', scrollY > 30);
        bt.classList.toggle('show', scrollY > 400);
    });
    const ham = document.getElementById('ham');
    const hamIcon = document.getElementById('hamIcon');
    const mobNav = document.getElementById('mobNav');
    ham.addEventListener('click', () => {
        mobNav.classList.toggle('open');
        hamIcon.className = mobNav.classList.contains('open') ? 'fas fa-times' : 'fas fa-bars';
    });
    function closeMob(){ mobNav.classList.remove('open'); hamIcon.className='fas fa-bars'; }
</script>
</body>
</html>
