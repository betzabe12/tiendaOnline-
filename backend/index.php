<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Si el usuario inició sesión correctamente:
if (isset($_SESSION['usuario'])) {
    $nombre = htmlspecialchars($_SESSION['usuario']['nombre'] ?? 'Usuario');
    $correo = htmlspecialchars($_SESSION['usuario']['correo'] ?? '');
    $rol = htmlspecialchars($_SESSION['usuario']['rol'] ?? 'cliente');
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FashionCloud — Panel de Usuario</title>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Outfit', sans-serif;
                background-color: #F9FAFB;
                color: #111827;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                margin: 0;
                padding: 1.5rem;
            }
            .user-card {
                background: #FFFFFF;
                border: 1px solid #E5E7EB;
                border-radius: 12px;
                padding: 2.5rem 2rem;
                max-width: 440px;
                width: 100%;
                box-shadow: 0 10px 15px -3px rgba(0,0,0,0.06);
                text-align: center;
            }
            .brand-badge {
                display: inline-block;
                background-color: #FDF9EE;
                color: #C29B38;
                font-size: 0.8rem;
                font-weight: 700;
                padding: 0.35rem 0.85rem;
                border-radius: 9999px;
                margin-bottom: 1.25rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            h1 {
                font-size: 1.65rem;
                margin: 0 0 0.5rem 0;
            }
            p.user-email {
                color: #4B5563;
                font-size: 0.95rem;
                margin: 0 0 1.5rem 0;
            }
            .role-pill {
                display: inline-block;
                background: #E5E7EB;
                padding: 0.25rem 0.75rem;
                border-radius: 6px;
                font-size: 0.85rem;
                font-weight: 600;
                margin-bottom: 2rem;
            }
            .btn-group {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }
            .btn {
                display: block;
                padding: 0.75rem 1.25rem;
                border-radius: 6px;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.95rem;
                transition: 0.2s;
            }
            .btn-store {
                background-color: #111827;
                color: #FFFFFF;
            }
            .btn-store:hover {
                background-color: #374151;
            }
            .btn-logout {
                background-color: transparent;
                color: #EF4444;
                border: 1px solid #FCA5A5;
            }
            .btn-logout:hover {
                background-color: #FEE2E2;
            }
        </style>
    </head>
    <body>
        <div class="user-card">
            <span class="brand-badge">FashionCloud • Sesión Activa</span>
            <h1>¡Hola, <?= $nombre ?>!</h1>
            <p class="user-email"><?= $correo ?></p>
            <div>Rol asignado: <span class="role-pill"><?= ucfirst($rol) ?></span></div>
            
            <div class="btn-group">
                <a href="../frontend/index.html" class="btn btn-store">Ir al Catálogo de Ropa</a>
                <a href="controllers/AuthController.php?action=logout" class="btn btn-logout">Cerrar Sesión</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 2. Si hay mensaje flash (error o notificación de registro), lo mostramos y redirigimos:
if (isset($_SESSION['flash'])) {
    $mensaje = addslashes($_SESSION['flash']);
    unset($_SESSION['flash']);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>FashionCloud</title>
    </head>
    <body>
        <script>
            alert("<?= $mensaje ?>");
            window.location.href = "../frontend/index.html";
        </script>
    </body>
    </html>
    <?php
    exit;
}

// 3. Si no ha iniciado sesión y no hay flash, redirigimos a la landing page
header('Location: ../frontend/index.html');
exit;
