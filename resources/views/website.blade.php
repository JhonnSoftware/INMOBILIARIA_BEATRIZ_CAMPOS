<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beatriz Campos Inmobiliaria | Lotes en Hualhuas, Junín</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
        html{scroll-behavior:smooth;}
        body{font-family:'Poppins',sans-serif;color:#1a1a2e;overflow-x:hidden;}

        :root{
            --mg:#EE00BB;       /* magenta del logo */
            --mg2:#C4009A;      /* magenta oscuro */
            --vt:#5533CC;       /* violeta del texto logo */
            --vt2:#3D1F99;      /* violeta oscuro */
            --dark:#1a1a2e;
            --dark2:#16213e;
            --gray:#64748b;
            --light:#f8f9ff;
            --white:#ffffff;
        }

        /* ============================
           NAVBAR
        ============================ */
        .navbar{
            position:fixed;top:0;left:0;right:0;z-index:1000;
            background:rgba(255,255,255,0.97);
            backdrop-filter:blur(16px);
            border-bottom:1px solid rgba(85,51,204,0.08);
            transition:box-shadow .3s;
        }
        .navbar.shadow{box-shadow:0 4px 32px rgba(85,51,204,0.12);}
        .nav-wrap{
            max-width:1260px;margin:0 auto;padding:0 28px;
            height:70px;display:flex;align-items:center;justify-content:space-between;gap:20px;
        }
        .nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
        .nav-brand img{height:44px;width:44px;object-fit:contain;border-radius:8px;}
        .nav-brand-text{display:flex;flex-direction:column;line-height:1.1;}
        .nav-brand-text b{font-size:14px;font-weight:800;color:var(--vt);}
        .nav-brand-text span{font-size:9px;font-weight:600;letter-spacing:2.5px;color:var(--mg);text-transform:uppercase;}
        .nav-menu{display:flex;gap:2px;list-style:none;align-items:center;}
        .nav-menu a{
            padding:7px 14px;border-radius:8px;font-size:13px;font-weight:500;
            color:var(--dark);text-decoration:none;transition:all .2s;
        }
        .nav-menu a:hover{background:rgba(85,51,204,0.06);color:var(--vt);}
        .nav-wa{
            display:flex;align-items:center;gap:8px;background:#25D366;
            color:#fff;padding:9px 20px;border-radius:50px;
            font-size:13px;font-weight:700;text-decoration:none;
            transition:all .3s;box-shadow:0 4px 16px rgba(37,211,102,.3);
            white-space:nowrap;
        }
        .nav-wa:hover{background:#1db954;transform:translateY(-2px);box-shadow:0 8px 24px rgba(37,211,102,.4);}
        .nav-login{
            display:flex;align-items:center;gap:8px;
            background:linear-gradient(135deg,var(--vt),var(--vt2));
            color:#fff;padding:9px 20px;border-radius:50px;
            font-size:13px;font-weight:700;text-decoration:none;
            transition:all .3s;box-shadow:0 4px 16px rgba(85,51,204,.3);
            white-space:nowrap;
        }
        .nav-login:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(85,51,204,.45);}
        .hamburger{display:none;border:none;background:none;font-size:22px;color:var(--dark);cursor:pointer;}
        .mob-nav{
            display:none;flex-direction:column;gap:4px;
            position:fixed;top:70px;left:0;right:0;
            background:#fff;padding:16px 24px 24px;
            box-shadow:0 20px 40px rgba(0,0,0,.08);z-index:999;
            max-height:calc(100vh - 70px);overflow-y:auto;
        }
        .mob-nav.open{display:flex;}
        /* links normales del menú */
        .mob-nav a:not(.nav-wa):not(.nav-login){
            padding:11px 14px;border-radius:10px;font-size:14px;
            color:var(--dark);text-decoration:none;font-weight:500;transition:.2s;
        }
        .mob-nav a:not(.nav-wa):not(.nav-login):hover{background:var(--light);color:var(--vt);}
        /* botones de acción en el menú móvil */
        .mob-nav .nav-wa{margin-top:8px;justify-content:center;}
        .mob-nav .nav-login{margin-top:4px;justify-content:center;}

        /* ============================
           HERO — split layout
        ============================ */
        .hero{
            min-height:100vh;display:grid;grid-template-columns:1fr 1fr;
            padding-top:70px;overflow:hidden;
        }
        /* Lado izquierdo */
        .hero-left{
            background:linear-gradient(155deg,var(--dark2) 0%,var(--dark) 60%,#0f1535 100%);
            display:flex;flex-direction:column;justify-content:center;
            padding:60px 56px 60px 60px;position:relative;overflow:hidden;
        }
        .hero-left::before{
            content:'';position:absolute;top:-100px;left:-100px;
            width:400px;height:400px;border-radius:50%;
            background:radial-gradient(circle,rgba(238,0,187,.18) 0%,transparent 70%);
        }
        .hero-left::after{
            content:'';position:absolute;bottom:-80px;right:0;
            width:300px;height:300px;border-radius:50%;
            background:radial-gradient(circle,rgba(85,51,204,.2) 0%,transparent 70%);
        }
        .hero-pill{
            display:inline-flex;align-items:center;gap:8px;
            border:1px solid rgba(238,0,187,.35);background:rgba(238,0,187,.08);
            border-radius:50px;padding:7px 18px;
            color:rgba(255,255,255,.9);font-size:11.5px;font-weight:600;
            letter-spacing:1px;text-transform:uppercase;margin-bottom:22px;width:fit-content;
            position:relative;z-index:1;
        }
        .hero-pill .live{width:8px;height:8px;border-radius:50%;background:#25D366;animation:blink 1.4s infinite;}
        @keyframes blink{0%,100%{opacity:1;}50%{opacity:.3;}}
        .hero-h1{
            font-size:clamp(34px,4.5vw,60px);font-weight:900;color:#fff;
            line-height:1.08;margin-bottom:20px;position:relative;z-index:1;
        }
        .hero-h1 em{
            font-style:normal;
            background:linear-gradient(90deg,var(--mg),#ff66dd);
            -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
        }
        .hero-p{
            font-size:15px;color:rgba(255,255,255,.65);line-height:1.75;
            margin-bottom:36px;max-width:440px;position:relative;z-index:1;
        }
        .hero-btns{display:flex;gap:14px;flex-wrap:wrap;position:relative;z-index:1;margin-bottom:48px;}
        .btn-mg{
            display:inline-flex;align-items:center;gap:9px;
            background:linear-gradient(135deg,var(--mg),var(--mg2));
            color:#fff;padding:15px 32px;border-radius:50px;
            font-size:14px;font-weight:700;text-decoration:none;
            transition:all .3s;box-shadow:0 8px 28px rgba(238,0,187,.45);
        }
        .btn-mg:hover{transform:translateY(-3px);box-shadow:0 14px 36px rgba(238,0,187,.55);}
        .btn-ghost{
            display:inline-flex;align-items:center;gap:9px;
            border:2px solid rgba(255,255,255,.3);color:#fff;
            padding:13px 26px;border-radius:50px;
            font-size:14px;font-weight:600;text-decoration:none;transition:all .3s;
        }
        .btn-ghost:hover{border-color:#fff;background:rgba(255,255,255,.08);transform:translateY(-3px);}

        /* mini stats en hero */
        .hero-stats{
            display:flex;gap:28px;position:relative;z-index:1;
            border-top:1px solid rgba(255,255,255,.08);padding-top:32px;
        }
        .hs-item{display:flex;flex-direction:column;gap:3px;}
        .hs-num{font-size:28px;font-weight:900;color:var(--mg);line-height:1;}
        .hs-lbl{font-size:11px;color:rgba(255,255,255,.5);font-weight:400;}

        /* Lado derecho — foto principal */
        .hero-right{
            position:relative;overflow:hidden;
        }
        .hero-right img.hero-photo{
            width:100%;height:100%;object-fit:cover;
            display:block;
        }
        .hero-right-overlay{
            position:absolute;inset:0;
            background:linear-gradient(to right,rgba(26,26,46,.55) 0%,transparent 50%);
        }
        /* tarjeta flotante en la foto */
        .hero-float{
            position:absolute;bottom:36px;left:28px;right:28px;
            background:rgba(255,255,255,.95);backdrop-filter:blur(16px);
            border-radius:20px;padding:20px 22px;
            display:grid;grid-template-columns:repeat(3,1fr);gap:1px;
            box-shadow:0 12px 40px rgba(0,0,0,.18);
        }
        .hf-item{text-align:center;padding:8px 12px;}
        .hf-item:not(:last-child){border-right:1px solid rgba(0,0,0,.06);}
        .hf-price{font-size:20px;font-weight:900;color:var(--mg);line-height:1;}
        .hf-name{font-size:11px;font-weight:600;color:var(--dark);margin:3px 0 2px;}
        .hf-loc{font-size:10px;color:var(--gray);}

        /* ============================
           BADGES — servicios incluidos
        ============================ */
        .badges-bar{
            background:linear-gradient(135deg,var(--vt),var(--vt2));
            padding:18px 28px;
        }
        .badges-wrap{
            max-width:1260px;margin:0 auto;
            display:flex;align-items:center;justify-content:center;gap:10px;flex-wrap:wrap;
        }
        .badge{
            display:flex;align-items:center;gap:7px;
            background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.18);
            border-radius:50px;padding:8px 18px;
            color:#fff;font-size:12px;font-weight:600;
        }
        .badge i{font-size:13px;}

        /* ============================
           SECTION HELPERS
        ============================ */
        section{padding:88px 28px;}
        section.hero{padding:0;}
        .wrap{max-width:1260px;margin:0 auto;}
        .s-tag{
            display:inline-flex;align-items:center;gap:7px;
            border-radius:50px;padding:5px 16px;
            font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;
            margin-bottom:12px;
        }
        .s-tag-mg{background:rgba(238,0,187,.1);color:var(--mg2);}
        .s-tag-vt{background:rgba(85,51,204,.1);color:var(--vt);}
        .s-tag-wh{background:rgba(255,255,255,.12);color:rgba(255,255,255,.85);}
        .s-h2{font-size:clamp(26px,3.5vw,42px);font-weight:800;line-height:1.15;margin-bottom:14px;}
        .s-sub{font-size:15px;color:var(--gray);line-height:1.75;max-width:580px;}
        .s-head{margin-bottom:52px;}
        .s-head.center{text-align:center;}
        .s-head.center .s-sub{margin:0 auto;}

        /* ============================
           PROYECTOS
        ============================ */
        #proyectos{background:var(--light);}
        .proj-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:26px;}
        .proj-card{
            background:#fff;border-radius:22px;overflow:hidden;
            box-shadow:0 4px 24px rgba(0,0,0,.06);
            transition:all .35s cubic-bezier(.175,.885,.32,1.275);
            border:1px solid rgba(0,0,0,.04);
        }
        .proj-card:hover{transform:translateY(-10px);box-shadow:0 24px 60px rgba(85,51,204,.14);}
        .proj-img-wrap{position:relative;height:230px;overflow:hidden;}
        .proj-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .5s;}
        .proj-card:hover .proj-img-wrap img{transform:scale(1.07);}
        .proj-ribbon{
            position:absolute;top:16px;left:16px;
            background:linear-gradient(135deg,var(--mg),var(--mg2));
            color:#fff;font-size:10.5px;font-weight:700;
            padding:5px 14px;border-radius:50px;letter-spacing:.5px;
        }
        .proj-ribbon.new{background:linear-gradient(135deg,#f59e0b,#d97706);}
        .proj-body{padding:26px 24px;}
        .proj-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:8px;}
        .proj-name{font-size:18px;font-weight:800;color:var(--dark);line-height:1.2;}
        .proj-price-box{text-align:right;flex-shrink:0;margin-left:10px;}
        .proj-price{font-size:22px;font-weight:900;color:var(--mg);line-height:1;}
        .proj-psub{font-size:10px;color:var(--gray);font-weight:400;}
        .proj-loc{display:flex;align-items:center;gap:6px;font-size:12.5px;color:var(--gray);margin-bottom:16px;}
        .proj-loc i{color:var(--mg);font-size:12px;}
        .proj-features{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:20px;}
        .pf{
            display:flex;align-items:center;gap:5px;
            background:var(--light);border-radius:50px;
            padding:4px 12px;font-size:11px;font-weight:500;color:var(--dark);
        }
        .pf i{font-size:11px;}
        .pf-agua i{color:#3b82f6;}
        .pf-luz i{color:#f59e0b;}
        .pf-des i{color:#10b981;}
        .pf-alum i{color:#8b5cf6;}
        .pf-fin i{color:var(--mg);}
        .pf-m2 i{color:var(--vt);}
        .proj-btn{
            display:flex;align-items:center;justify-content:center;gap:8px;
            width:100%;padding:13px;border-radius:14px;
            font-size:13.5px;font-weight:700;text-decoration:none;
            background:linear-gradient(135deg,var(--vt),var(--vt2));
            color:#fff;transition:all .3s;
            box-shadow:0 6px 20px rgba(85,51,204,.25);
        }
        .proj-btn:hover{box-shadow:0 12px 32px rgba(85,51,204,.4);transform:translateY(-2px);}

        /* ============================
           POR QUÉ NOSOTROS
        ============================ */
        #por-que{
            background:var(--dark2);
            position:relative;overflow:hidden;
        }
        #por-que::before{
            content:'';position:absolute;right:-120px;top:-120px;
            width:500px;height:500px;border-radius:50%;
            background:radial-gradient(circle,rgba(238,0,187,.1) 0%,transparent 70%);
        }
        .why-grid{display:grid;grid-template-columns:1fr 1fr;gap:40px;align-items:center;}
        .why-img-col{position:relative;}
        .why-img-main{width:100%;border-radius:24px;object-fit:cover;box-shadow:0 20px 60px rgba(0,0,0,.3);}
        .why-img-over{
            position:absolute;bottom:-24px;right:-24px;
            width:180px;border-radius:18px;
            border:4px solid var(--dark2);
            box-shadow:0 12px 36px rgba(0,0,0,.3);
        }
        .why-badge{
            position:absolute;top:20px;left:20px;
            background:linear-gradient(135deg,var(--mg),var(--mg2));
            color:#fff;border-radius:14px;padding:12px 18px;
            font-size:13px;font-weight:700;
            box-shadow:0 8px 24px rgba(238,0,187,.4);
        }
        .why-badge span{display:block;font-size:28px;font-weight:900;line-height:1;}
        .why-content{}
        .why-content .s-h2{color:#fff;}
        .why-content .s-sub{color:rgba(255,255,255,.55);}
        .why-list{display:flex;flex-direction:column;gap:16px;margin-top:32px;}
        .wl-item{
            display:flex;align-items:flex-start;gap:16px;
            background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.07);
            border-radius:18px;padding:18px 20px;transition:.3s;
        }
        .wl-item:hover{background:rgba(238,0,187,.08);border-color:rgba(238,0,187,.2);}
        .wl-icon{
            width:44px;height:44px;border-radius:13px;flex-shrink:0;
            display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff;
        }
        .wli-mg{background:linear-gradient(135deg,var(--mg),var(--mg2));}
        .wli-vt{background:linear-gradient(135deg,var(--vt),var(--vt2));}
        .wli-gn{background:linear-gradient(135deg,#10b981,#059669);}
        .wli-bl{background:linear-gradient(135deg,#3b82f6,#1d4ed8);}
        .wl-text .wt-title{font-size:14.5px;font-weight:700;color:#fff;margin-bottom:4px;}
        .wl-text .wt-desc{font-size:12.5px;color:rgba(255,255,255,.5);line-height:1.6;}

        /* ============================
           GALERÍA
        ============================ */
        #galeria{background:var(--light);}
        .gal-grid{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            grid-template-rows:repeat(2,240px);
            gap:14px;
        }
        .gal-item{border-radius:18px;overflow:hidden;position:relative;cursor:pointer;}
        .gal-item.tall{grid-row:span 2;}
        .gal-item img{width:100%;height:100%;object-fit:cover;transition:transform .5s;display:block;}
        .gal-item:hover img{transform:scale(1.08);}
        .gal-over{
            position:absolute;inset:0;
            background:linear-gradient(to top,rgba(26,26,46,.8) 0%,transparent 55%);
            opacity:0;transition:.3s;
            display:flex;align-items:flex-end;padding:18px;
        }
        .gal-item:hover .gal-over{opacity:1;}
        .gal-over span{color:#fff;font-size:12.5px;font-weight:500;}

        /* ============================
           PASOS
        ============================ */
        #pasos{
            background:linear-gradient(135deg,var(--vt2) 0%,var(--vt) 50%,var(--mg2) 100%);
        }
        #pasos .s-h2{color:#fff;}
        #pasos .s-sub{color:rgba(255,255,255,.65);}
        .steps-row{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;position:relative;}
        .steps-row::before{display:none;}
        .step{
            background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);
            border-radius:22px;padding:30px 22px;text-align:center;
            transition:.3s;position:relative;z-index:1;
        }
        .step:hover{background:rgba(255,255,255,.16);border-color:rgba(255,255,255,.28);}
        .step-n{
            width:52px;height:52px;border-radius:50%;
            background:#fff;color:var(--vt);
            font-size:20px;font-weight:900;
            display:flex;align-items:center;justify-content:center;
            margin:0 auto 16px;
            box-shadow:0 8px 24px rgba(0,0,0,.2);
        }
        .step-icon{font-size:26px;color:rgba(255,255,255,.7);margin-bottom:12px;}
        .step-title{font-size:15px;font-weight:700;color:#fff;margin-bottom:8px;}
        .step-desc{font-size:12px;color:rgba(255,255,255,.55);line-height:1.65;}

        /* ============================
           TESTIMONIO
        ============================ */
        #testimonio{background:#fff;}
        .testi-wrap{
            display:grid;grid-template-columns:1.2fr 1fr;gap:56px;align-items:center;
        }
        .testi-content{}
        .testi-stars{color:#f59e0b;font-size:20px;margin-bottom:18px;}
        .testi-quote{
            font-size:22px;font-weight:700;color:var(--dark);
            line-height:1.55;margin-bottom:24px;font-style:italic;
        }
        .testi-quote::before{
            content:'\201C';color:var(--mg);font-size:64px;
            line-height:0.3;vertical-align:-0.5em;display:inline-block;margin-right:4px;
        }
        .testi-author{
            display:flex;align-items:center;gap:14px;
            padding:16px 20px;background:var(--light);border-radius:16px;
            margin-bottom:28px;border-left:4px solid var(--mg);
        }
        .ta-av{
            width:52px;height:52px;border-radius:50%;
            background:linear-gradient(135deg,var(--mg),var(--vt));
            display:flex;align-items:center;justify-content:center;
            color:#fff;font-size:20px;flex-shrink:0;
        }
        .ta-n{font-size:15px;font-weight:700;color:var(--dark);}
        .ta-r{font-size:12px;color:var(--gray);}
        .testi-checks{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
        .tc{
            display:flex;align-items:center;gap:9px;
            background:var(--light);padding:12px 16px;border-radius:12px;
        }
        .tc i{color:var(--mg);font-size:15px;flex-shrink:0;}
        .tc span{font-size:12.5px;font-weight:500;color:var(--dark);}
        .testi-photo{position:relative;}
        .testi-photo img{
            width:100%;border-radius:24px;object-fit:cover;
            box-shadow:0 24px 64px rgba(238,0,187,.18);
        }
        .testi-float-badge{
            position:absolute;bottom:-18px;right:-18px;
            background:linear-gradient(135deg,var(--mg),var(--vt));
            color:#fff;border-radius:18px;padding:16px 22px;text-align:center;
            box-shadow:0 12px 32px rgba(238,0,187,.4);
        }
        .tfb-num{font-size:32px;font-weight:900;line-height:1;}
        .tfb-txt{font-size:11px;font-weight:600;opacity:.85;}

        /* ============================
           UBICACIÓN
        ============================ */
        /* ============================
           UBICACIÓN — nuevo diseño
        ============================ */
        #ubicacion{
            background:linear-gradient(160deg,var(--dark2) 0%,var(--dark) 100%);
            position:relative;overflow:hidden;
        }
        #ubicacion::before{
            content:'';position:absolute;left:-150px;bottom:-150px;
            width:500px;height:500px;border-radius:50%;
            background:radial-gradient(circle,rgba(85,51,204,.15) 0%,transparent 70%);
        }
        #ubicacion .s-h2{color:#fff;}
        #ubicacion .s-sub{color:rgba(255,255,255,.55);}
        #ubicacion .s-tag-vt{background:rgba(255,255,255,.1);color:rgba(255,255,255,.8);}
        /* mapas en fila */
        .ubic-maps-row{
            display:grid;grid-template-columns:1fr 1fr;gap:20px;
            margin-bottom:28px;
        }
        .ubic-map-card{
            position:relative;border-radius:22px;overflow:hidden;
            height:300px;box-shadow:0 16px 48px rgba(0,0,0,.4);
            transition:.3s;
        }
        .ubic-map-card:hover{transform:translateY(-4px);box-shadow:0 24px 60px rgba(0,0,0,.5);}
        .ubic-map-card img{width:100%;height:100%;object-fit:cover;display:block;}
        .ubic-map-label{
            position:absolute;top:0;left:0;right:0;
            background:linear-gradient(to bottom,rgba(26,26,46,.85) 0%,transparent 100%);
            padding:18px 20px 40px;
        }
        .ubic-map-label span{
            display:inline-flex;align-items:center;gap:7px;
            background:linear-gradient(135deg,var(--mg),var(--vt));
            color:#fff;font-size:12px;font-weight:700;
            padding:6px 14px;border-radius:50px;letter-spacing:.5px;
        }
        .ubic-map-badge{
            position:absolute;bottom:0;left:0;right:0;
            background:linear-gradient(to top,rgba(26,26,46,.9) 0%,transparent 100%);
            padding:40px 20px 16px;
            color:rgba(255,255,255,.85);font-size:12px;font-weight:500;
            display:flex;align-items:center;gap:7px;
        }
        .ubic-map-badge i{color:var(--mg);}
        /* info cards en fila */
        .ubic-cards-row{
            display:grid;grid-template-columns:repeat(5,1fr);gap:14px;
        }
        .ubic-card{
            background:rgba(255,255,255,.06);
            border:1px solid rgba(255,255,255,.08);
            border-radius:18px;padding:20px 18px;
            display:flex;flex-direction:column;gap:10px;
            transition:.3s;
        }
        .ubic-card:hover{
            background:rgba(238,0,187,.08);
            border-color:rgba(238,0,187,.25);
            transform:translateY(-4px);
        }
        .ubic-ic{
            width:42px;height:42px;border-radius:12px;flex-shrink:0;
            background:linear-gradient(135deg,var(--mg),var(--vt));
            display:flex;align-items:center;justify-content:center;
            color:#fff;font-size:16px;
        }
        .ubic-t{font-size:13px;font-weight:700;color:#fff;margin-bottom:2px;}
        .ubic-v{font-size:11.5px;color:rgba(255,255,255,.5);line-height:1.65;}
        @media(max-width:1024px){
            .ubic-cards-row{grid-template-columns:repeat(3,1fr);}
        }
        @media(max-width:768px){
            .ubic-maps-row{grid-template-columns:1fr;}
            .ubic-map-card{height:240px;}
            .ubic-cards-row{grid-template-columns:1fr 1fr;}
        }
        @media(max-width:480px){
            .ubic-cards-row{grid-template-columns:1fr;}
        }

        /* ============================
           CTA FINAL
        ============================ */
        #cta{
            background:var(--dark);
            position:relative;overflow:hidden;text-align:center;
        }
        #cta::before{
            content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
            width:700px;height:700px;border-radius:50%;
            background:radial-gradient(circle,rgba(238,0,187,.12) 0%,transparent 65%);
        }
        #cta .s-h2{color:#fff;position:relative;z-index:1;}
        #cta .s-sub{color:rgba(255,255,255,.55);margin:0 auto 40px;position:relative;z-index:1;}
        .cta-contacts{
            display:flex;gap:20px;justify-content:center;flex-wrap:wrap;
            margin-bottom:40px;position:relative;z-index:1;
        }
        .cta-c{
            display:flex;flex-direction:column;align-items:center;gap:8px;
            background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
            border-radius:18px;padding:24px 32px;text-decoration:none;
            transition:.3s;min-width:170px;
        }
        .cta-c:hover{background:rgba(255,255,255,.1);border-color:rgba(255,255,255,.2);transform:translateY(-4px);}
        .cta-c i{font-size:28px;margin-bottom:4px;}
        .cta-c .cc-t{font-size:13px;font-weight:600;color:#fff;}
        .cta-c .cc-v{font-size:12px;color:rgba(255,255,255,.5);}
        .cta-btns{display:flex;gap:16px;justify-content:center;flex-wrap:wrap;position:relative;z-index:1;}
        .btn-wa-big{
            display:inline-flex;align-items:center;gap:12px;
            background:#25D366;color:#fff;padding:17px 40px;
            border-radius:50px;font-size:16px;font-weight:700;
            text-decoration:none;transition:.3s;
            box-shadow:0 8px 28px rgba(37,211,102,.4);
        }
        .btn-wa-big:hover{background:#1db954;transform:translateY(-3px);box-shadow:0 16px 40px rgba(37,211,102,.5);}
        .btn-acc{
            display:inline-flex;align-items:center;gap:12px;
            border:2px solid rgba(255,255,255,.3);color:#fff;
            padding:15px 34px;border-radius:50px;font-size:15px;
            font-weight:600;text-decoration:none;transition:.3s;
        }
        .btn-acc:hover{border-color:#fff;background:rgba(255,255,255,.08);transform:translateY(-3px);}

        /* ============================
           FOOTER
        ============================ */
        footer{background:#0c0c1a;padding:56px 28px 28px;}
        .footer-wrap{max-width:1260px;margin:0 auto;}
        .footer-top{
            display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px;
            padding-bottom:36px;border-bottom:1px solid rgba(255,255,255,.06);margin-bottom:24px;
        }
        .f-brand a{display:flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:14px;}
        .f-brand a img{height:42px;width:42px;object-fit:contain;border-radius:8px;}
        .f-brand a span{font-size:15px;font-weight:800;color:#fff;}
        .f-brand p{font-size:12.5px;color:rgba(255,255,255,.45);line-height:1.8;max-width:260px;}
        .f-social{display:flex;gap:8px;margin-top:16px;}
        .fs-btn{
            width:36px;height:36px;border-radius:9px;
            background:rgba(255,255,255,.06);
            display:flex;align-items:center;justify-content:center;
            color:rgba(255,255,255,.5);text-decoration:none;font-size:14px;transition:.3s;
        }
        .fs-btn:hover{background:var(--mg);color:#fff;transform:translateY(-2px);}
        .f-col h4{font-size:13px;font-weight:700;color:#fff;margin-bottom:14px;}
        .f-links{list-style:none;display:flex;flex-direction:column;gap:9px;}
        .f-links a{font-size:12.5px;color:rgba(255,255,255,.45);text-decoration:none;transition:.2s;}
        .f-links a:hover{color:rgba(238,0,187,.8);}
        .footer-bottom{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;}
        .footer-bottom p{font-size:12px;color:rgba(255,255,255,.35);}
        .footer-bottom strong{color:var(--mg);}
        .f-ver{background:rgba(238,0,187,.15);color:rgba(238,0,187,.8);padding:4px 14px;border-radius:50px;font-size:11px;font-weight:600;letter-spacing:.5px;}

        /* ============================
           FLOATING BUTTONS
        ============================ */
        .float-wa{position:fixed;bottom:84px;right:26px;z-index:500;}
        .float-wa a{
            display:flex;align-items:center;gap:8px;
            background:#25D366;color:#fff;
            padding:11px 20px;border-radius:50px;
            font-size:13px;font-weight:700;text-decoration:none;
            box-shadow:0 8px 28px rgba(37,211,102,.45);transition:.3s;
        }
        .float-wa a:hover{transform:scale(1.05);}
        .float-wa i{font-size:20px;}
        .back-top{
            position:fixed;bottom:28px;right:26px;z-index:500;
            width:46px;height:46px;border-radius:13px;
            background:linear-gradient(135deg,var(--mg),var(--vt));
            display:flex;align-items:center;justify-content:center;
            color:#fff;font-size:17px;text-decoration:none;
            box-shadow:0 8px 24px rgba(238,0,187,.4);
            opacity:0;pointer-events:none;transition:.3s;
        }
        .back-top.show{opacity:1;pointer-events:all;}
        .back-top:hover{transform:translateY(-4px);}

        /* ============================
           RESPONSIVE
        ============================ */
        @media(max-width:1100px){
            .hero{grid-template-columns:1fr;}
            .hero-right{height:55vw;min-height:360px;}
            .hero-left{padding:48px 40px;}
            .proj-grid{grid-template-columns:1fr 1fr;}
            .why-grid{grid-template-columns:1fr;}
            .why-img-col{max-width:500px;margin:0 auto;}
            .footer-top{grid-template-columns:1fr 1fr;}
        }
        @media(max-width:768px){
            .nav-menu,.nav-wa,.nav-login{display:none;}
            .hamburger{display:block;}
            section{padding:64px 20px;}
            .hero-left{padding:40px 24px;}
            .hero-btns{flex-direction:column;}
            .hero-float{grid-template-columns:1fr;gap:0;}
            .hf-item:not(:last-child){border-right:none;border-bottom:1px solid rgba(0,0,0,.06);}
            .gal-grid{grid-template-columns:1fr 1fr;grid-template-rows:repeat(3,190px);}
            .gal-item.tall{grid-row:span 2;}
            .steps-row{grid-template-columns:1fr 1fr;}
            .steps-row::before{display:none;}
            .testi-wrap{grid-template-columns:1fr;}
            .ubic-grid{grid-template-columns:1fr;}
            .cta-contacts{gap:12px;}
            .footer-top{grid-template-columns:1fr;}
        }
        @media(max-width:480px){
            .proj-grid{grid-template-columns:1fr;}
            .gal-grid{grid-template-columns:1fr;}
            .gal-item.tall{grid-row:span 1;height:220px;}
            .gal-item{height:200px;}
            .steps-row{grid-template-columns:1fr;}
            .hero-stats{gap:18px;}
            .hs-num{font-size:22px;}
            .float-wa a span{display:none;}
            .float-wa a{padding:11px;border-radius:50%;width:48px;height:48px;justify-content:center;}
        }
    </style>
</head>
<body>

<!-- ========== NAVBAR ========== -->
<nav class="navbar" id="navbar">
    <div class="nav-wrap">
        <a href="#inicio" class="nav-brand">
            <img src="{{ asset('imagenes/inmobiliaria_bc.jpeg') }}" alt="Logo Beatriz Campos">
            <div class="nav-brand-text">
                <b>Beatriz Campos</b>
                <span>Inmobiliaria</span>
            </div>
        </a>
        <ul class="nav-menu">
            <li><a href="#inicio">Inicio</a></li>
            <li><a href="#proyectos">Proyectos</a></li>
            <li><a href="#por-que">Nosotros</a></li>
            <li><a href="#galeria">Galería</a></li>
            <li><a href="#ubicacion">Ubicación</a></li>
            <li><a href="#cta">Contacto</a></li>
        </ul>
        <a href="https://wa.me/51900000000" class="nav-wa" target="_blank">
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
    <a href="#inicio" onclick="closeMob()">Inicio</a>
    <a href="#proyectos" onclick="closeMob()">Proyectos</a>
    <a href="#por-que" onclick="closeMob()">Nosotros</a>
    <a href="#galeria" onclick="closeMob()">Galería</a>
    <a href="#ubicacion" onclick="closeMob()">Ubicación</a>
    <a href="#cta" onclick="closeMob()">Contacto</a>
    <a href="https://wa.me/51900000000" class="nav-wa" target="_blank">
        <i class="fab fa-whatsapp"></i> WhatsApp
    </a>
    <a href="{{ url('/acceso') }}" class="nav-login" style="margin-top:4px;justify-content:center;">
        <i class="fas fa-sign-in-alt"></i> Ingresar al Sistema
    </a>
</div>

<!-- ========== HERO ========== -->
<section id="inicio" class="hero">
    <!-- izquierda: texto -->
    <div class="hero-left">
        <div class="hero-pill">
            <span class="live"></span> Proyectos disponibles ahora
        </div>
        <h1 class="hero-h1">
            Tu <em>lote propio</em><br>en Hualhuas<br>está aquí
        </h1>
        <p class="hero-p">
            Lotes con Habilitación Urbana completa, financiamiento
            sin intereses y todos los servicios básicos incluidos.
            Haz realidad el sueño de tu familia hoy.
        </p>
        <div class="hero-btns">
            <a href="#proyectos" class="btn-mg">
                <i class="fas fa-home"></i> Ver Proyectos
            </a>
            <a href="#cta" class="btn-ghost">
                <i class="fas fa-phone"></i> Más Información
            </a>
        </div>
        <div class="hero-stats">
            <div class="hs-item">
                <span class="hs-num">3+</span>
                <span class="hs-lbl">Proyectos activos</span>
            </div>
            <div class="hs-item">
                <span class="hs-num">100+</span>
                <span class="hs-lbl">Familias atendidas</span>
            </div>
            <div class="hs-item">
                <span class="hs-num">0%</span>
                <span class="hs-lbl">Intereses</span>
            </div>
        </div>
    </div>

    <!-- derecha: foto + tarjeta flotante -->
    <div class="hero-right">
        <img class="hero-photo"
             src="{{ asset('imagenes/imagenes_website/proyecto-carretera-central-75k.jpeg') }}"
             alt="Proyecto Carretera Central - Beatriz Campos">
        <div class="hero-right-overlay"></div>
        <div class="hero-float">
            <div class="hf-item">
                <div class="hf-price">S/. 35,000</div>
                <div class="hf-name">Lotes Hualhuas</div>
                <div class="hf-loc"><i class="fas fa-map-marker-alt" style="color:var(--mg);"></i> Av. 13 de Diciembre</div>
            </div>
            <div class="hf-item">
                <div class="hf-price">S/. 50,000</div>
                <div class="hf-name">Calle Principal</div>
                <div class="hf-loc"><i class="fas fa-map-marker-alt" style="color:var(--mg);"></i> Calle Huancayo</div>
            </div>
            <div class="hf-item">
                <div class="hf-price">S/. 75,000</div>
                <div class="hf-name">Carretera Central</div>
                <div class="hf-loc"><i class="fas fa-map-marker-alt" style="color:var(--mg);"></i> Óvalo Hualhuas</div>
            </div>
        </div>
    </div>
</section>

<!-- ========== BADGES ========== -->
<div class="badges-bar">
    <div class="badges-wrap">
        <div class="badge"><i class="fas fa-tint"></i> Agua potable</div>
        <div class="badge"><i class="fas fa-bolt"></i> Luz eléctrica</div>
        <div class="badge"><i class="fas fa-soap"></i> Desagüe</div>
        <div class="badge"><i class="fas fa-lightbulb"></i> Alumbrado público</div>
        <div class="badge"><i class="fas fa-hand-holding-usd"></i> Financiamiento sin intereses</div>
        <div class="badge"><i class="fas fa-file-contract"></i> Habilitación urbana</div>
        <div class="badge"><i class="fas fa-check-double"></i> Proyectos 100% ejecutados</div>
    </div>
</div>

<!-- ========== PROYECTOS ========== -->
<section id="proyectos">
    <div class="wrap">
        <div class="s-head">
            <span class="s-tag s-tag-mg"><i class="fas fa-map-marker-alt"></i> &nbsp;Disponibles ahora</span>
            <h2 class="s-h2">Nuestros <span style="color:var(--mg);">Proyectos</span></h2>
            <p class="s-sub">Elige el proyecto ideal para tu familia. Todos incluyen habilitación urbana completa y financiamiento sin intereses.</p>
        </div>
        <div class="proj-grid">

            <!-- Proyecto 1 -->
            <div class="proj-card">
                <div class="proj-img-wrap">
                    <img src="{{ asset('imagenes/imagenes_website/proyecto-lotes-hualhuas-35k.jpeg') }}"
                         alt="Lotes Hualhuas">
                    <span class="proj-ribbon new">Nuevo Proyecto</span>
                </div>
                <div class="proj-body">
                    <div class="proj-top">
                        <h3 class="proj-name">Lotes Hualhuas</h3>
                        <div class="proj-price-box">
                            <div class="proj-price">S/. 35,000</div>
                            <div class="proj-psub">desde / lote</div>
                        </div>
                    </div>
                    <div class="proj-loc"><i class="fas fa-map-marker-alt"></i> Av. 13 de Diciembre, Hualhuas Chauca</div>
                    <div class="proj-features">
                        <span class="pf pf-agua"><i class="fas fa-tint"></i> Agua</span>
                        <span class="pf pf-luz"><i class="fas fa-bolt"></i> Luz</span>
                        <span class="pf pf-des"><i class="fas fa-soap"></i> Desagüe</span>
                        <span class="pf pf-alum"><i class="fas fa-lightbulb"></i> Alumbrado</span>
                        <span class="pf pf-m2"><i class="fas fa-ruler-combined"></i> 100 m²</span>
                        <span class="pf pf-fin"><i class="fas fa-hand-holding-usd"></i> Sin intereses</span>
                    </div>
                    <a href="https://wa.me/51900000000?text=Hola!%20Me%20interesa%20Lotes%20Hualhuas%20desde%20S%2F35%2C000" class="proj-btn" target="_blank">
                        <i class="fab fa-whatsapp"></i> Consultar por WhatsApp
                    </a>
                </div>
            </div>

            <!-- Proyecto 2 -->
            <div class="proj-card">
                <div class="proj-img-wrap">
                    <img src="{{ asset('imagenes/imagenes_website/proyecto-calle-principal-50k.jpeg') }}"
                         alt="Calle Principal Huancayo">
                    <span class="proj-ribbon">Disponible</span>
                </div>
                <div class="proj-body">
                    <div class="proj-top">
                        <h3 class="proj-name">Calle Principal</h3>
                        <div class="proj-price-box">
                            <div class="proj-price">S/. 50,000</div>
                            <div class="proj-psub">por lote</div>
                        </div>
                    </div>
                    <div class="proj-loc"><i class="fas fa-map-marker-alt"></i> Calle Huancayo, Hualhuas</div>
                    <div class="proj-features">
                        <span class="pf pf-agua"><i class="fas fa-tint"></i> Agua</span>
                        <span class="pf pf-luz"><i class="fas fa-bolt"></i> Luz</span>
                        <span class="pf pf-des"><i class="fas fa-soap"></i> Desagüe</span>
                        <span class="pf pf-m2"><i class="fas fa-ruler-combined"></i> 100 m²</span>
                        <span class="pf pf-fin"><i class="fas fa-hand-holding-usd"></i> Sin intereses</span>
                    </div>
                    <a href="https://wa.me/51900000000?text=Hola!%20Me%20interesa%20el%20proyecto%20Calle%20Principal%20S%2F50%2C000" class="proj-btn" target="_blank">
                        <i class="fab fa-whatsapp"></i> Consultar por WhatsApp
                    </a>
                </div>
            </div>

            <!-- Proyecto 3 -->
            <div class="proj-card">
                <div class="proj-img-wrap">
                    <img src="{{ asset('imagenes/imagenes_website/proyecto-calle-chauca-35k.jpeg') }}"
                         alt="Carretera Central">
                    <span class="proj-ribbon">Disponible</span>
                </div>
                <div class="proj-body">
                    <div class="proj-top">
                        <h3 class="proj-name">Carretera Central</h3>
                        <div class="proj-price-box">
                            <div class="proj-price">S/. 75,000</div>
                            <div class="proj-psub">por lote</div>
                        </div>
                    </div>
                    <div class="proj-loc"><i class="fas fa-map-marker-alt"></i> Óvalo de Hualhuas, Carretera Central</div>
                    <div class="proj-features">
                        <span class="pf pf-agua"><i class="fas fa-tint"></i> Agua</span>
                        <span class="pf pf-luz"><i class="fas fa-bolt"></i> Luz</span>
                        <span class="pf pf-des"><i class="fas fa-soap"></i> Desagüe</span>
                        <span class="pf pf-m2"><i class="fas fa-ruler-combined"></i> 100 m²</span>
                        <span class="pf pf-fin"><i class="fas fa-hand-holding-usd"></i> Sin intereses</span>
                    </div>
                    <a href="https://wa.me/51900000000?text=Hola!%20Me%20interesa%20Carretera%20Central%20S%2F75%2C000" class="proj-btn" target="_blank">
                        <i class="fab fa-whatsapp"></i> Consultar por WhatsApp
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ========== POR QUÉ NOSOTROS ========== -->
<section id="por-que">
    <div class="wrap">
        <div class="why-grid">
            <!-- Imagen -->
            <div class="why-img-col">
                <img class="why-img-main"
                     src="{{ asset('imagenes/imagenes_website/obra-vista-aerea.jpeg') }}"
                     alt="Avance de obras">
                <img class="why-img-over"
                     src="{{ asset('imagenes/imagenes_website/obra-encofrado.jpeg') }}"
                     alt="Encofrado de veredas">
                <div class="why-badge">
                    <span>100%</span>
                    Ejecutado
                </div>
            </div>
            <!-- Contenido -->
            <div class="why-content">
                <span class="s-tag s-tag-wh"><i class="fas fa-star"></i> &nbsp;Por qué elegirnos</span>
                <h2 class="s-h2">Comprometidos con<br>tu <span style="color:var(--mg);">inversión</span></h2>
                <p class="s-sub" style="color:rgba(255,255,255,.55);">
                    Cada proyecto que iniciamos lo terminamos al 100%. Tu lote llega con todos los servicios y garantías.
                </p>
                <div class="why-list">
                    <div class="wl-item">
                        <div class="wl-icon wli-mg"><i class="fas fa-check-double"></i></div>
                        <div class="wl-text">
                            <div class="wt-title">Proyectos 100% Ejecutados</div>
                            <div class="wt-desc">Encofrado de veredas, instalación de tuberías, alumbrado — todo lo prometido se cumple.</div>
                        </div>
                    </div>
                    <div class="wl-item">
                        <div class="wl-icon wli-gn"><i class="fas fa-hand-holding-usd"></i></div>
                        <div class="wl-text">
                            <div class="wt-title">Financiamiento Sin Intereses</div>
                            <div class="wt-desc">Planes flexibles y accesibles. Sin letras escondidas ni intereses adicionales de ningún tipo.</div>
                        </div>
                    </div>
                    <div class="wl-item">
                        <div class="wl-icon wli-bl"><i class="fas fa-city"></i></div>
                        <div class="wl-text">
                            <div class="wt-title">Habilitación Urbana Completa</div>
                            <div class="wt-desc">Agua, luz, desagüe y alumbrado público en cada lote. Listo para construir desde el día 1.</div>
                        </div>
                    </div>
                    <div class="wl-item">
                        <div class="wl-icon wli-vt"><i class="fas fa-shield-alt"></i></div>
                        <div class="wl-text">
                            <div class="wt-title">Respaldo Legal y Documentación</div>
                            <div class="wt-desc">Contrato notarial, títulos de propiedad en regla. Tu inversión completamente protegida.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== GALERÍA ========== -->
<section id="galeria">
    <div class="wrap">
        <div class="s-head center">
            <span class="s-tag s-tag-vt"><i class="fas fa-images"></i> &nbsp;Avances de obra</span>
            <h2 class="s-h2">Así avanza tu <span style="color:var(--vt);">proyecto</span></h2>
            <p class="s-sub">Transparencia total. Mira en tiempo real cómo construimos el lugar que pronto será tuyo.</p>
        </div>
        <div class="gal-grid">
            <!-- item grande -->
            <div class="gal-item tall">
                <img src="{{ asset('imagenes/imagenes_website/proyecto-lotes-vista-aerea.jpeg') }}"
                     alt="Vista aérea del proyecto">
                <div class="gal-over"><span><i class="fas fa-expand-alt"></i> &nbsp;Vista aérea del proyecto</span></div>
            </div>
            <!-- resto -->
            <div class="gal-item">
                <img src="{{ asset('imagenes/imagenes_website/obra-encofrado.jpeg') }}"
                     alt="Encofrado de veredas">
                <div class="gal-over"><span>Encofrado de veredas en cada frontis</span></div>
            </div>
            <div class="gal-item">
                <img src="{{ asset('imagenes/imagenes_website/obra-tuberia-desague.jpeg') }}"
                     alt="Tubería de desagüe">
                <div class="gal-over"><span>Instalación de tuberías de desagüe</span></div>
            </div>
            <div class="gal-item">
                <img src="{{ asset('imagenes/imagenes_website/obra-movimiento-tierra.jpeg') }}"
                     alt="Movimiento de tierra">
                <div class="gal-over"><span>Habilitación y movimiento de tierras</span></div>
            </div>
            <div class="gal-item">
                <img src="{{ asset('imagenes/imagenes_website/obra-buzon-desague.jpeg') }}"
                     alt="Colocación buzón de desagüe">
                <div class="gal-over"><span>Colocación del buzón de desagüe</span></div>
            </div>
        </div>
    </div>
</section>

<!-- ========== PASOS ========== -->
<section id="pasos">
    <div class="wrap">
        <div class="s-head center">
            <span class="s-tag s-tag-wh"><i class="fas fa-route"></i> &nbsp;Proceso simple</span>
            <h2 class="s-h2">¿Cómo adquirir<br>tu <span style="background:linear-gradient(90deg,#ff88ee,var(--mg));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">lote</span>?</h2>
            <p class="s-sub">Solo 4 pasos separan a tu familia del terreno propio.</p>
        </div>
        <div class="steps-row">
            <div class="step">
                <div class="step-n">1</div>
                <div class="step-icon"><i class="fas fa-phone-alt"></i></div>
                <h3 class="step-title">Contáctanos</h3>
                <p class="step-desc">Llámanos o escríbenos por WhatsApp. Un asesor te atenderá de inmediato con toda la información.</p>
            </div>
            <div class="step">
                <div class="step-n">2</div>
                <div class="step-icon"><i class="fas fa-map-marked-alt"></i></div>
                <h3 class="step-title">Visita el lote</h3>
                <p class="step-desc">Agenda una visita guiada y conoce personalmente el terreno, los avances y la ubicación.</p>
            </div>
            <div class="step">
                <div class="step-n">3</div>
                <div class="step-icon"><i class="fas fa-file-signature"></i></div>
                <h3 class="step-title">Firma el contrato</h3>
                <p class="step-desc">Elegiste tu lote. Firmamos el contrato notarial con todas las garantías legales correspondientes.</p>
            </div>
            <div class="step">
                <div class="step-n">4</div>
                <div class="step-icon"><i class="fas fa-key"></i></div>
                <h3 class="step-title">¡Es tuyo!</h3>
                <p class="step-desc">Recibe tu lote con todos los servicios listos. El futuro de tu familia comienza aquí. ¡Felicidades!</p>
            </div>
        </div>
    </div>
</section>

<!-- ========== TESTIMONIO ========== -->
<section id="testimonio">
    <div class="wrap">
        <div class="testi-wrap">
            <div class="testi-content">
                <span class="s-tag s-tag-mg"><i class="fas fa-quote-left"></i> &nbsp;Testimonios</span>
                <h2 class="s-h2">Familias que ya<br>viven su <span style="color:var(--mg);">sueño</span></h2>
                <div class="testi-stars">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    <i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="testi-quote">
                    Felicidades por hacer realidad el sueño de adquirir su lote. Hoy dan un gran paso hacia el futuro de su familia. ¡Tú también puedes!
                </p>
                <div class="testi-author">
                    <div class="ta-av"><i class="fas fa-users"></i></div>
                    <div>
                        <div class="ta-n">Familia Vitman y Esposa</div>
                        <div class="ta-r">Propietarios — Familia #252 atendida</div>
                    </div>
                </div>
                <div class="testi-checks">
                    <div class="tc"><i class="fas fa-check-circle"></i><span>Proceso rápido</span></div>
                    <div class="tc"><i class="fas fa-check-circle"></i><span>Financiamiento accesible</span></div>
                    <div class="tc"><i class="fas fa-check-circle"></i><span>Atención personalizada</span></div>
                    <div class="tc"><i class="fas fa-check-circle"></i><span>Documentos en regla</span></div>
                </div>
            </div>
            <div class="testi-photo">
                <img src="{{ asset('imagenes/imagenes_website/testimonio-familia-vitman.jpeg') }}"
                     alt="Familia Vitman - clientes felices">
                <div class="testi-float-badge">
                    <div class="tfb-num">252+</div>
                    <div class="tfb-txt">Familias<br>Felices</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== UBICACIÓN ========== -->
<section id="ubicacion">
    <div class="wrap">
        <div class="s-head center">
            <span class="s-tag s-tag-vt"><i class="fas fa-map-marker-alt"></i> &nbsp;Ubicación</span>
            <h2 class="s-h2">¿Cómo <span style="color:var(--mg);">llegar</span>?</h2>
            <p class="s-sub">Fácil acceso desde Huancayo. Nuestros proyectos están en zonas estratégicas de Hualhuas, Junín.</p>
        </div>

        <!-- Mapas en fila -->
        <div class="ubic-maps-row">
            <div class="ubic-map-card">
                <img src="{{ asset('imagenes/imagenes_website/mapa-llegar.jpeg') }}" alt="Cómo llegar a tu lote">
                <div class="ubic-map-label">
                    <span><i class="fas fa-route"></i> Cómo llegar a tu lote</span>
                </div>
                <div class="ubic-map-badge">
                    <i class="fas fa-map-marker-alt"></i> Carretera Central → Jirón Los Guindales → Tu Lote
                </div>
            </div>
            <div class="ubic-map-card">
                <img src="{{ asset('imagenes/imagenes_website/mapa-hualhuas.jpeg') }}" alt="Nuevo Proyecto Hualhuas">
                <div class="ubic-map-label">
                    <span><i class="fas fa-map"></i> Nuevo Proyecto — Hualhuas</span>
                </div>
                <div class="ubic-map-badge">
                    <i class="fas fa-map-marker-alt"></i> Av. 13 de Diciembre · Ref: Parque de Chauca
                </div>
            </div>
        </div>

        <!-- Tarjetas de info -->
        <div class="ubic-cards-row">
            <div class="ubic-card">
                <div class="ubic-ic"><i class="fas fa-building"></i></div>
                <div class="ubic-t">Oficina Principal</div>
                <div class="ubic-v">Jr. 28 de Julio N° 495<br>Huancayo, Junín</div>
            </div>
            <div class="ubic-card">
                <div class="ubic-ic"><i class="fas fa-map-pin"></i></div>
                <div class="ubic-t">Lotes Hualhuas</div>
                <div class="ubic-v">Av. 13 de Diciembre con Calle sin Nombre, Hualhuas Chauca</div>
            </div>
            <div class="ubic-card">
                <div class="ubic-ic"><i class="fas fa-map-pin"></i></div>
                <div class="ubic-t">Calle Principal</div>
                <div class="ubic-v">Calle Alfonso Ugarte y 13 de Diciembre, Hualhuas Chauca</div>
            </div>
            <div class="ubic-card">
                <div class="ubic-ic"><i class="fas fa-map-pin"></i></div>
                <div class="ubic-t">Carretera Central</div>
                <div class="ubic-v">Carretera Central, Óvalo de Hualhuas — acceso directo</div>
            </div>
            <div class="ubic-card">
                <div class="ubic-ic"><i class="fas fa-bus"></i></div>
                <div class="ubic-t">En Combi</div>
                <div class="ubic-v">Paradero Combis de Hualhuas, Av. 13 de Diciembre. Ref: Parque de Chauca</div>
            </div>
        </div>
    </div>
</section>

<!-- ========== CTA FINAL ========== -->
<section id="cta">
    <div class="wrap">
        <div style="position:relative;z-index:1;text-align:center;">
            <span class="s-tag s-tag-wh" style="margin-bottom:16px;display:inline-flex;">
                <i class="fas fa-headset"></i> &nbsp;Estamos para ayudarte
            </span>
            <h2 class="s-h2" style="color:#fff;margin-bottom:14px;">
                ¿Listo para dar el<br><span style="background:linear-gradient(90deg,var(--mg),#ff88ee);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">gran paso</span>?
            </h2>
            <p class="s-sub" style="color:rgba(255,255,255,.55);margin:0 auto 40px;">
                Contáctanos ahora. Un asesor te atenderá personalmente y te guiará en todo el proceso de adquisición.
            </p>
            <div class="cta-contacts">
                <a href="https://wa.me/51900000000" class="cta-c" target="_blank">
                    <i class="fab fa-whatsapp" style="color:#25D366;"></i>
                    <div class="cc-t">WhatsApp</div>
                    <div class="cc-v">Escríbenos ahora</div>
                </a>
                <a href="tel:+51900000000" class="cta-c">
                    <i class="fas fa-phone-alt" style="color:rgba(238,0,187,.8);"></i>
                    <div class="cc-t">Llámanos</div>
                    <div class="cc-v">+51 900 000 000</div>
                </a>
                <a href="https://www.facebook.com/" class="cta-c" target="_blank">
                    <i class="fab fa-facebook" style="color:#1877F2;"></i>
                    <div class="cc-t">Facebook</div>
                    <div class="cc-v">Beatriz Campos Inmob.</div>
                </a>
            </div>
            <div class="cta-btns">
                <a href="https://wa.me/51900000000?text=Hola!%20Quiero%20informaci%C3%B3n%20sobre%20los%20lotes%20disponibles" class="btn-wa-big" target="_blank">
                    <i class="fab fa-whatsapp"></i> Escribir por WhatsApp
                </a>
                <a href="{{ url('/acceso') }}" class="btn-acc">
                    <i class="fas fa-sign-in-alt"></i> Acceso al Sistema
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ========== FOOTER ========== -->
<footer>
    <div class="footer-wrap">
        <div class="footer-top">
            <div class="f-brand">
                <a href="#inicio">
                    <img src="{{ asset('imagenes/inmobiliaria_bc.jpeg') }}" alt="Logo BC Inmobiliaria">
                    <span>Beatriz Campos Inmobiliaria</span>
                </a>
                <p>Hacemos realidad el sueño de miles de familias en Junín. Lotes con habilitación urbana completa y financiamiento sin intereses.</p>
                <div class="f-social">
                    <a href="#" class="fs-btn"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="fs-btn"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="fs-btn"><i class="fab fa-tiktok"></i></a>
                    <a href="https://wa.me/51900000000" class="fs-btn" target="_blank"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="f-col">
                <h4>Proyectos</h4>
                <ul class="f-links">
                    <li><a href="#proyectos">Lotes Hualhuas — S/. 35,000</a></li>
                    <li><a href="#proyectos">Calle Principal — S/. 50,000</a></li>
                    <li><a href="#proyectos">Carretera Central — S/. 75,000</a></li>
                </ul>
            </div>
            <div class="f-col">
                <h4>Empresa</h4>
                <ul class="f-links">
                    <li><a href="#por-que">Por qué elegirnos</a></li>
                    <li><a href="#galeria">Galería de obras</a></li>
                    <li><a href="#testimonio">Testimonios</a></li>
                    <li><a href="#pasos">Cómo comprar</a></li>
                </ul>
            </div>
            <div class="f-col">
                <h4>Contacto</h4>
                <ul class="f-links">
                    <li><a href="#">Jr. 28 de Julio N° 495</a></li>
                    <li><a href="#">Huancayo, Junín, Perú</a></li>
                    <li><a href="tel:+51900000000">+51 900 000 000</a></li>
                    <li><a href="https://wa.me/51900000000">WhatsApp</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 <strong>Beatriz Campos Inmobiliaria</strong>. Todos los derechos reservados.</p>
            <span class="f-ver">BC INMOBILIARIA v1.0</span>
        </div>
    </div>
</footer>

<!-- FLOATING WA -->
<div class="float-wa">
    <a href="https://wa.me/51900000000?text=Hola!%20Quiero%20informaci%C3%B3n%20sobre%20los%20lotes" target="_blank">
        <i class="fab fa-whatsapp"></i>
        <span>¡Escríbenos!</span>
    </a>
</div>
<a href="#inicio" class="back-top" id="backTop"><i class="fas fa-chevron-up"></i></a>

<script>
    // navbar shadow on scroll + back-to-top
    const nav = document.getElementById('navbar');
    const bt  = document.getElementById('backTop');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('shadow', scrollY > 30);
        bt.classList.toggle('show', scrollY > 400);
    });

    // hamburger menu
    const ham     = document.getElementById('ham');
    const hamIcon = document.getElementById('hamIcon');
    const mobNav  = document.getElementById('mobNav');
    ham.addEventListener('click', () => {
        mobNav.classList.toggle('open');
        hamIcon.className = mobNav.classList.contains('open') ? 'fas fa-times' : 'fas fa-bars';
    });
    function closeMob(){
        mobNav.classList.remove('open');
        hamIcon.className = 'fas fa-bars';
    }

    // animate on scroll
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if(e.isIntersecting){
                e.target.style.opacity = '1';
                e.target.style.transform = 'translateY(0)';
                obs.unobserve(e.target);
            }
        });
    }, {threshold: 0.1});
    document.querySelectorAll('.proj-card,.wl-item,.step,.ubic-card,.gal-item').forEach((el,i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(28px)';
        el.style.transition = `opacity .5s ${(i%4)*.1}s, transform .5s ${(i%4)*.1}s`;
        obs.observe(el);
    });
</script>
</body>
</html>
