<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beatriz Campos Inmobiliaria</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #FF1493 0%, #C020A0 25%, #8B2FC9 55%, #4A1080 80%, #1C1B33 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.25;
            animation: drift 8s ease-in-out infinite;
        }
        .orb-1 { width: 500px; height: 500px; background: #FF1493; top: -150px; right: -150px; animation-delay: 0s; }
        .orb-2 { width: 400px; height: 400px; background: #7B2DD4; bottom: -120px; left: -120px; animation-delay: 3s; }
        .orb-3 { width: 300px; height: 300px; background: #FF69B4; top: 40%; left: 5%; animation-delay: 1.5s; }
        .orb-4 { width: 250px; height: 250px; background: #9B59B6; top: 20%; right: 15%; animation-delay: 4s; }

        @keyframes drift {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(15px, -20px) scale(1.05); }
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            animation: float 7s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.5; }
            50% { transform: translateY(-30px) rotate(180deg); opacity: 1; }
        }

        .card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 32px;
            padding: 52px 48px;
            width: 500px;
            max-width: 92vw;
            box-shadow:
                0 40px 100px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            position: relative;
            z-index: 10;
            animation: cardEntrance 0.7s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0)   scale(1);    }
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 32px; right: 32px;
            height: 4px;
            background: linear-gradient(90deg, #FF1493, #7B2DD4, #FF1493);
            border-radius: 0 0 4px 4px;
        }

        .logo-wrapper {
            background: linear-gradient(135deg, #FF1493 0%, #C020A0 50%, #7B2DD4 100%);
            border-radius: 24px;
            padding: 22px 24px;
            margin: 0 auto 28px;
            width: 210px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 16px 40px rgba(255, 20, 147, 0.45), 0 4px 12px rgba(0,0,0,0.15);
            position: relative;
            overflow: hidden;
        }
        .logo-wrapper::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 50%;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 24px 24px 0 0;
        }
        .logo-wrapper img {
            width: 100%;
            border-radius: 14px;
            position: relative;
            z-index: 1;
        }

        .company-name {
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #1C1B33;
            margin-bottom: 4px;
        }
        .company-name .highlight { color: #FF1493; }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0 26px;
        }
        .divider-line {
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, #e8e8e8);
        }
        .divider-line.right {
            background: linear-gradient(to left, transparent, #e8e8e8);
        }
        .divider-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #FF1493, #7B2DD4);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }

        .welcome-title {
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            background: linear-gradient(135deg, #7B2DD4, #C020A0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .welcome-subtitle {
            text-align: center;
            color: #9CA3AF;
            font-size: 13.5px;
            font-weight: 400;
            margin-bottom: 34px;
            line-height: 1.6;
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .btn {
            display: flex;
            align-items: center;
            gap: 16px;
            width: 100%;
            padding: 18px 22px;
            border: none;
            border-radius: 16px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        .btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(rgba(255,255,255,0.18), rgba(255,255,255,0));
            opacity: 0;
            transition: opacity 0.3s;
        }
        .btn:hover::before { opacity: 1; }
        .btn:hover { transform: translateY(-4px); }
        .btn:active { transform: translateY(-1px); transition-duration: 0.1s; }

        .btn-icon-wrap {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
            color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .btn-content { flex: 1; text-align: left; }
        .btn-content .label { display: block; color: white; font-weight: 600; font-size: 15px; }
        .btn-content .desc { display: block; color: rgba(255,255,255,0.75); font-size: 11px; font-weight: 400; margin-top: 2px; }

        .btn-arrow { color: rgba(255,255,255,0.6); font-size: 13px; transition: transform 0.3s; }
        .btn:hover .btn-arrow { transform: translateX(4px); color: rgba(255,255,255,0.9); }

        .btn-cliente {
            background: linear-gradient(135deg, #FF1493 0%, #E0006A 100%);
            box-shadow: 0 10px 30px rgba(255, 20, 147, 0.45), inset 0 1px 0 rgba(255,255,255,0.2);
        }
        .btn-cliente:hover {
            box-shadow: 0 18px 45px rgba(255, 20, 147, 0.55), inset 0 1px 0 rgba(255,255,255,0.2);
        }

        .btn-admin {
            background: linear-gradient(135deg, #7B2DD4 0%, #5B1DB4 100%);
            box-shadow: 0 10px 30px rgba(123, 45, 212, 0.45), inset 0 1px 0 rgba(255,255,255,0.2);
        }
        .btn-admin:hover {
            box-shadow: 0 18px 45px rgba(123, 45, 212, 0.55), inset 0 1px 0 rgba(255,255,255,0.2);
        }

        .card-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #F3F4F6;
        }
        .card-footer p { color: #C4C4C4; font-size: 11.5px; }
        .card-footer strong { color: #FF1493; font-weight: 600; }
        .card-footer .version {
            display: inline-block;
            background: linear-gradient(135deg, rgba(255,20,147,0.08), rgba(123,45,212,0.08));
            color: #7B2DD4;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 10px;
            font-weight: 600;
            margin-top: 6px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="orb orb-4"></div>

    <div class="particle" style="width:90px;height:90px;top:8%;left:12%;animation-delay:0s;animation-duration:8s;"></div>
    <div class="particle" style="width:50px;height:50px;top:75%;left:8%;animation-delay:1.5s;animation-duration:6s;"></div>
    <div class="particle" style="width:70px;height:70px;top:15%;right:18%;animation-delay:3s;animation-duration:9s;"></div>
    <div class="particle" style="width:110px;height:110px;bottom:10%;right:8%;animation-delay:0.8s;animation-duration:7s;"></div>
    <div class="particle" style="width:35px;height:35px;top:55%;left:4%;animation-delay:4s;animation-duration:5s;"></div>
    <div class="particle" style="width:60px;height:60px;bottom:30%;right:20%;animation-delay:2s;animation-duration:10s;"></div>

    <div class="card">

        <div class="logo-wrapper">
            <img src="{{ asset('imagenes/inmobiliaria_bc.jpeg') }}" alt="Beatriz Campos Inmobiliaria">
        </div>

        <div class="company-name">
            <span class="highlight">Beatriz Campos</span> Inmobiliaria
        </div>

        <div class="divider">
            <div class="divider-line"></div>
            <div class="divider-icon"><i class="fas fa-home"></i></div>
            <div class="divider-line right"></div>
        </div>

        <h1 class="welcome-title">Bienvenido al Sistema</h1>
        <p class="welcome-subtitle">
            Selecciona el tipo de acceso para continuar<br>
            con la plataforma de gestión inmobiliaria
        </p>

        <div class="btn-group">
            <a href="/cliente" class="btn btn-cliente">
                <div class="btn-icon-wrap">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="btn-content">
                    <span class="label">Acceso Cliente</span>
                    <span class="desc">Ver propiedades y mis contratos</span>
                </div>
                <i class="fas fa-chevron-right btn-arrow"></i>
            </a>

            <a href="/admin" class="btn btn-admin">
                <div class="btn-icon-wrap">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="btn-content">
                    <span class="label">Acceso Administrativo</span>
                    <span class="desc">Gestión completa del sistema</span>
                </div>
                <i class="fas fa-chevron-right btn-arrow"></i>
            </a>
        </div>

        <div class="card-footer">
            <p>&copy; 2025 <strong>Beatriz Campos Inmobiliaria</strong>. Todos los derechos reservados.</p>
            <span class="version">SISTEMA v1.0</span>
        </div>
    </div>

</body>
</html>
