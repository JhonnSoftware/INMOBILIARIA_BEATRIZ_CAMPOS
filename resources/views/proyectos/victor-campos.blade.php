@extends('layouts.web')

@section('title', 'Residencial Victor Campos - Beatriz Campos Inmobiliaria')

@section('head')
<style>
    body{padding-top:70px;}
    .wrap{max-width:1260px;margin:0 auto;padding:0 28px;}
    section{padding:72px 0;}

    .proy-hero{
        background:linear-gradient(160deg,#0d0521 0%,#1a0845 50%,#230c55 100%);
        padding:80px 28px 64px;position:relative;overflow:hidden;
    }
    .proy-hero::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(238,0,187,.05) 1px,transparent 1px),linear-gradient(90deg,rgba(238,0,187,.05) 1px,transparent 1px);background-size:48px 48px;}
    .proy-hero-wrap{max-width:1260px;margin:0 auto;display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;position:relative;z-index:1;}
    .proy-back{display:inline-flex;align-items:center;gap:7px;color:rgba(255,255,255,.6);text-decoration:none;font-size:13px;font-weight:500;margin-bottom:20px;transition:.2s;}
    .proy-back:hover{color:var(--mg);}
    .proy-tag{display:inline-flex;align-items:center;gap:7px;background:rgba(238,0,187,.12);border:1px solid rgba(238,0,187,.3);border-radius:50px;padding:6px 16px;color:rgba(255,255,255,.9);font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin-bottom:18px;}
    .proy-h1{font-size:clamp(32px,4vw,52px);font-weight:900;color:#fff;line-height:1.1;margin-bottom:16px;}
    .proy-h1 span{background:linear-gradient(90deg,var(--mg),#ff88ee);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
    .proy-desc{font-size:15px;color:rgba(255,255,255,.65);line-height:1.75;margin-bottom:28px;max-width:500px;}
    .proy-hero-img{border-radius:24px;overflow:hidden;box-shadow:0 32px 80px rgba(0,0,0,.5);position:relative;}
    .proy-hero-img img{width:100%;height:380px;object-fit:cover;display:block;}
    .proy-hero-badge{position:absolute;top:20px;left:20px;background:linear-gradient(135deg,var(--mg),var(--mg2));color:#fff;padding:8px 18px;border-radius:50px;font-size:12px;font-weight:700;}

    .proy-features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:0;}
    .pf-card{background:#fff;border-radius:18px;padding:28px 24px;border:1px solid rgba(0,0,0,.06);box-shadow:0 4px 20px rgba(0,0,0,.05);display:flex;flex-direction:column;gap:10px;transition:.3s;}
    .pf-card:hover{transform:translateY(-4px);box-shadow:0 12px 36px rgba(85,51,204,.1);}
    .pf-card-ic{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;}
    .pf-card-ic i{font-size:22px;}
    .pf-card-ic.agua{background:#eff6ff;} .pf-card-ic.agua i{color:#3b82f6;}
    .pf-card-ic.luz{background:#fffbeb;} .pf-card-ic.luz i{color:#f59e0b;}
    .pf-card-ic.des{background:#f0fdf4;} .pf-card-ic.des i{color:#10b981;}
    .pf-card-ic.road{background:#f5f3ff;} .pf-card-ic.road i{color:#8b5cf6;}
    .pf-card-ic.doc{background:#fff7ed;} .pf-card-ic.doc i{color:#f97316;}
    .pf-card-ic.fin{background:#fdf2f8;} .pf-card-ic.fin i{color:var(--mg);}
    .pf-card-t{font-size:14px;font-weight:700;color:#1a1a2e;}
    .pf-card-v{font-size:12.5px;color:#64748b;line-height:1.5;}

    #galeria{background:#f8f9ff;}
    .gal-grid-p{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
    .gal-item-p{border-radius:16px;overflow:hidden;height:220px;}
    .gal-item-p img{width:100%;height:100%;object-fit:cover;transition:.4s;}
    .gal-item-p:hover img{transform:scale(1.07);}

    #mapa{background:#fff;}
    .mapa-box{border-radius:24px;overflow:hidden;box-shadow:0 16px 48px rgba(0,0,0,.1);border:1px solid rgba(0,0,0,.06);}
    .mapa-box img{width:100%;height:420px;object-fit:cover;display:block;}
    .mapa-cta{margin-top:24px;text-align:center;}
    .btn-maps{display:inline-flex;align-items:center;gap:10px;background:linear-gradient(135deg,var(--vt),var(--vt2));color:#fff;padding:14px 32px;border-radius:50px;font-size:14px;font-weight:700;text-decoration:none;box-shadow:0 8px 24px rgba(85,51,204,.35);transition:.3s;}
    .btn-maps:hover{transform:translateY(-3px);box-shadow:0 14px 36px rgba(85,51,204,.45);}

    #cta-proy{background:linear-gradient(160deg,#0d0521,#1a0845);}
    #cta-proy .s-h2{color:#fff;}
    .btn-wa-big{display:inline-flex;align-items:center;gap:12px;background:#25D366;color:#fff;padding:17px 40px;border-radius:50px;font-size:16px;font-weight:700;text-decoration:none;box-shadow:0 8px 28px rgba(37,211,102,.4);transition:.3s;}
    .btn-wa-big:hover{background:#1db954;transform:translateY(-3px);}

    .s-tag{display:inline-flex;align-items:center;gap:7px;border-radius:50px;padding:5px 16px;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:12px;}
    .s-tag-mg{background:rgba(238,0,187,.1);color:var(--mg2);}
    .s-tag-vt{background:rgba(85,51,204,.1);color:var(--vt);}
    .s-h2{font-size:clamp(24px,3vw,38px);font-weight:800;line-height:1.15;margin-bottom:14px;}
    .s-sub{font-size:15px;color:#64748b;line-height:1.75;max-width:580px;}
    .s-head{margin-bottom:48px;}
    .s-head.center{text-align:center;} .s-head.center .s-sub{margin:0 auto;}

    @media(max-width:900px){
        .proy-hero-wrap{grid-template-columns:1fr;}
        .proy-features-grid{grid-template-columns:1fr 1fr;}
        .gal-grid-p{grid-template-columns:1fr 1fr;}
    }
    @media(max-width:600px){
        .proy-hero{padding:60px 20px 48px;}
        .proy-features-grid{grid-template-columns:1fr;}
        .gal-grid-p{grid-template-columns:1fr;}
        .mapa-box img{height:260px;}
    }
</style>
@endsection

@section('content')
<div id="top"></div>

<section class="proy-hero">
    <div class="proy-hero-wrap">
        <div>
            <a href="{{ url('/') }}#proyectos" class="proy-back"><i class="fas fa-arrow-left"></i> Volver a Proyectos</a>
            <div class="proy-tag"><i class="fas fa-city"></i> &nbsp;Proyecto de Lotizacion</div>
            <h1 class="proy-h1">Residencial<br><span>Victor Campos</span></h1>
            <p class="proy-desc">Proyecto residencial de lotizacion ubicado en Hualhuas, Junin. Lotes con servicios instalados, documentos en regla y financiamiento sin intereses para que compres con mayor tranquilidad.</p>
            <div style="display:flex;gap:14px;flex-wrap:wrap;">
                <a href="https://wa.me/51929303999?text=Hola!%20Me%20interesa%20el%20Residencial%20Victor%20Campos" target="_blank" class="btn-wa-big" style="font-size:14px;padding:13px 28px;">
                    <i class="fab fa-whatsapp"></i> Consultar ahora
                </a>
                <a href="https://share.google/lFcRRIYcJ7vsVtgM7" target="_blank" class="btn-maps">
                    <i class="fas fa-map-marked-alt"></i> Ver en Maps
                </a>
            </div>
        </div>
        <div class="proy-hero-img">
            <img src="{{ asset('imagenes/imagenes_website/proyecto-carretera-central-75k.jpeg') }}" alt="Residencial Victor Campos">
            <div class="proy-hero-badge"><i class="fas fa-check-circle"></i> Disponible</div>
        </div>
    </div>
</section>

<section style="background:#f8f9ff;">
    <div class="wrap">
        <div class="s-head center">
            <span class="s-tag s-tag-mg"><i class="fas fa-list-check"></i> &nbsp;Servicios incluidos</span>
            <h2 class="s-h2">Todo lo que incluye<br>tu <span style="color:var(--mg);">lote</span></h2>
            <p class="s-sub">Cada lote viene con todos los servicios instalados y documentos en regla para que solo te preocupes de construir tu hogar.</p>
        </div>
        <div class="proy-features-grid">
            <div class="pf-card">
                <div class="pf-card-ic agua"><i class="fas fa-tint"></i></div>
                <div class="pf-card-t">Agua Potable</div>
                <div class="pf-card-v">Red de agua potable instalada y operativa en todo el proyecto.</div>
            </div>
            <div class="pf-card">
                <div class="pf-card-ic luz"><i class="fas fa-bolt"></i></div>
                <div class="pf-card-t">Luz Electrica</div>
                <div class="pf-card-v">Conexion electrica domiciliaria con postes y cableado instalado.</div>
            </div>
            <div class="pf-card">
                <div class="pf-card-ic des"><i class="fas fa-soap"></i></div>
                <div class="pf-card-t">Desague</div>
                <div class="pf-card-v">Red de desague completa con buzones y colectores instalados.</div>
            </div>
            <div class="pf-card">
                <div class="pf-card-ic road"><i class="fas fa-road"></i></div>
                <div class="pf-card-t">Pistas y Veredas</div>
                <div class="pf-card-v">Habilitacion urbana completa con vias internas y accesos consolidados.</div>
            </div>
            <div class="pf-card">
                <div class="pf-card-ic doc"><i class="fas fa-file-contract"></i></div>
                <div class="pf-card-t">Documentos en Regla</div>
                <div class="pf-card-v">Titulo de propiedad, independizacion y documentacion lista para una compra segura.</div>
            </div>
            <div class="pf-card">
                <div class="pf-card-ic fin"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="pf-card-t">Financiamiento 0%</div>
                <div class="pf-card-v">Cuotas mensuales comodas sin intereses ni costos ocultos.</div>
            </div>
        </div>
    </div>
</section>

<section id="galeria">
    <div class="wrap">
        <div class="s-head">
            <span class="s-tag s-tag-vt"><i class="fas fa-images"></i> &nbsp;Avance de obras</span>
            <h2 class="s-h2">Fotos del <span style="color:var(--mg);">proyecto</span></h2>
            <p class="s-sub">Conoce imagenes referenciales y de avance del proyecto para evaluar mejor su entorno y estado actual.</p>
        </div>
        <div class="gal-grid-p">
            <div class="gal-item-p"><img src="{{ asset('imagenes/imagenes_website/proyecto-carretera-central-75k.jpeg') }}" alt="Vista del proyecto Victor Campos"></div>
            <div class="gal-item-p"><img src="{{ asset('imagenes/imagenes_website/obra-vista-aerea.jpeg') }}" alt="Vista aerea del avance"></div>
            <div class="gal-item-p"><img src="{{ asset('imagenes/imagenes_website/obra-encofrado.jpeg') }}" alt="Obra de encofrado"></div>
            <div class="gal-item-p"><img src="{{ asset('imagenes/imagenes_website/obra-tuberia-desague.jpeg') }}" alt="Instalacion de tuberia de desague"></div>
            <div class="gal-item-p"><img src="{{ asset('imagenes/imagenes_website/obra-movimiento-tierra.jpeg') }}" alt="Movimiento de tierra"></div>
            <div class="gal-item-p"><img src="{{ asset('imagenes/imagenes_website/obra-buzon-desague.jpeg') }}" alt="Buzon de desague"></div>
        </div>
    </div>
</section>

<section id="mapa">
    <div class="wrap">
        <div class="s-head center">
            <span class="s-tag s-tag-vt"><i class="fas fa-map-marker-alt"></i> &nbsp;Ubicacion</span>
            <h2 class="s-h2">Donde esta el <span style="color:var(--mg);">proyecto</span>?</h2>
            <p class="s-sub">Hualhuas, Junin, con acceso rapido desde Huancayo y conexion directa por las vias principales de la zona.</p>
        </div>
        <div class="mapa-box">
            <img src="{{ asset('imagenes/imagenes_website/mapa-hualhuas.jpeg') }}" alt="Mapa ubicacion Residencial Victor Campos">
        </div>
        <div class="mapa-cta">
            <a href="https://share.google/lFcRRIYcJ7vsVtgM7" target="_blank" class="btn-maps" style="margin-top:0;">
                <i class="fas fa-map-marked-alt"></i> Abrir mapa completo en Google Maps
            </a>
        </div>
    </div>
</section>

<section id="cta-proy">
    <div class="wrap">
        <div style="text-align:center;">
            <span class="s-tag" style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.85);margin-bottom:16px;display:inline-flex;">
                <i class="fas fa-calendar-check"></i> &nbsp;Visita sin compromiso
            </span>
            <h2 class="s-h2" style="color:#fff;margin-bottom:12px;">Te interesa <span style="color:var(--mg);">Residencial Victor Campos</span>?</h2>
            <p class="s-sub" style="color:rgba(255,255,255,.55);margin:0 auto 36px;">Escribenos ahora y un asesor te atendera personalmente para resolver tus dudas y coordinar una visita.</p>
            <a href="https://wa.me/51929303999?text=Hola!%20Me%20interesa%20el%20Residencial%20Victor%20Campos%20y%20quisiera%20agendar%20una%20visita" target="_blank" class="btn-wa-big">
                <i class="fab fa-whatsapp"></i> Agendar visita gratis
            </a>
        </div>
    </div>
</section>
@endsection
