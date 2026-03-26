<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso — Beatriz Campos Inmobiliaria</title>
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
            background:rgba(255,255,255,0.12);
            backdrop-filter:blur(12px);
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
            padding:48px 56px;
            position:relative;
        }
        .right::before{
            content:'';position:absolute;top:0;left:0;right:0;height:4px;
            background:linear-gradient(90deg,#EE00BB,#5533CC,#EE00BB);
        }
        .right-inner{width:100%;max-width:380px;}

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

        /* cards de acceso */
        .access-cards{display:flex;flex-direction:column;gap:16px;margin-bottom:28px;}

        .access-card{
            display:flex;align-items:center;gap:18px;
            padding:20px 22px;border-radius:18px;
            text-decoration:none;border:2px solid transparent;
            transition:all .3s cubic-bezier(.175,.885,.32,1.275);
            position:relative;overflow:hidden;
        }
        .access-card::after{
            content:'';position:absolute;inset:0;
            background:linear-gradient(rgba(255,255,255,.12),rgba(255,255,255,0));
            opacity:0;transition:.3s;
        }
        .access-card:hover{transform:translateY(-4px);}
        .access-card:hover::after{opacity:1;}
        .access-card:active{transform:translateY(-1px);}

        .ac-cliente{
            background:linear-gradient(135deg,#EE00BB,#C4009A);
            box-shadow:0 10px 32px rgba(238,0,187,.35);
        }
        .ac-cliente:hover{box-shadow:0 18px 48px rgba(238,0,187,.5);}

        .ac-admin{
            background:linear-gradient(135deg,#5533CC,#3D1F99);
            box-shadow:0 10px 32px rgba(85,51,204,.35);
        }
        .ac-admin:hover{box-shadow:0 18px 48px rgba(85,51,204,.5);}

        .ac-icon{
            width:52px;height:52px;border-radius:14px;flex-shrink:0;
            background:rgba(255,255,255,.18);
            display:flex;align-items:center;justify-content:center;
            font-size:22px;color:#fff;
            box-shadow:0 4px 12px rgba(0,0,0,.15);
        }
        .ac-text{flex:1;}
        .ac-text .ac-title{display:block;font-size:16px;font-weight:700;color:#fff;margin-bottom:3px;}
        .ac-text .ac-desc{display:block;font-size:11.5px;color:rgba(255,255,255,.7);font-weight:400;}
        .ac-arrow{
            width:34px;height:34px;border-radius:50%;
            background:rgba(255,255,255,.15);
            display:flex;align-items:center;justify-content:center;
            color:rgba(255,255,255,.8);font-size:13px;
            transition:all .3s;flex-shrink:0;
        }
        .access-card:hover .ac-arrow{background:rgba(255,255,255,.25);transform:translateX(3px);color:#fff;}

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
        .right-footer{
            text-align:center;margin-top:28px;
            padding-top:20px;border-top:1px solid #f1f5f9;
        }
        .right-footer p{font-size:11px;color:#cbd5e1;}
        .right-footer strong{color:#EE00BB;}

        /* ===== RESPONSIVE ===== */
        @media(max-width:900px){
            body{grid-template-columns:1fr;}
            .left{
                padding:36px 28px;
                min-height:auto;
            }
            .left::before,.left::after{display:none;}
            .brand-logo-wrap{width:100px;height:100px;border-radius:24px;margin-bottom:16px;}
            .brand-name{font-size:20px;}
            .brand-stats,.brand-chips{display:none;}
            .brand-sub{margin-bottom:0;}
            .back-link{margin-top:16px;}
            .right{padding:36px 24px 40px;}
            .right::before{height:3px;}
        }

        @media(max-width:480px){
            .right{padding:28px 20px 36px;}
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
                <img src="{{ asset('imagenes/inmobiliaria_bc.jpeg') }}" alt="Beatriz Campos Inmobiliaria">
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
                Selecciona tu<br><span>tipo de acceso</span>
            </h1>
            <p class="right-sub">
                Elige el portal correspondiente para ingresar a la plataforma de gestión inmobiliaria.
            </p>

            <div class="access-cards">

                <a href="{{ url('/cliente') }}" class="access-card ac-cliente">
                    <div class="ac-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="ac-text">
                        <span class="ac-title">Portal Cliente</span>
                        <span class="ac-desc">Ver mis propiedades y contratos</span>
                    </div>
                    <div class="ac-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

                <a href="{{ url('/admin') }}" class="access-card ac-admin">
                    <div class="ac-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="ac-text">
                        <span class="ac-title">Panel Administrativo</span>
                        <span class="ac-desc">Gestión completa del sistema</span>
                    </div>
                    <div class="ac-arrow">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </a>

            </div>

            <div class="or-divider">
                <div class="or-line"></div>
                <div class="or-text">¿Tienes consultas?</div>
                <div class="or-line"></div>
            </div>

            <a href="https://wa.me/51900000000" class="wa-btn" target="_blank">
                <i class="fab fa-whatsapp"></i> Contáctanos por WhatsApp
            </a>

            <div class="right-footer">
                <p>&copy; 2026 <strong>Beatriz Campos Inmobiliaria</strong>. Todos los derechos reservados.</p>
            </div>

        </div>
    </div>

</body>
</html>
