
<?php
require_once __DIR__ . '/../config/app.php';

header('Content-Type: text/html; charset=UTF-8');

if (!isset($_SESSION['usuario'])) {
    fc_redirect('frontend/auth.php?modo=login');
}

if (($_SESSION['usuario']['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado. Esta página es exclusiva para administradores.');
}

$nombre = htmlspecialchars(
    $_SESSION['usuario']['nombre'] ?? 'Administrador',
    ENT_QUOTES,
    'UTF-8'
);

$csrfToken = fc_csrf_token();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel de Administración | FashionCloud</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            color: #333;
        }

        header {
            background: #252525;
            color: white;
            padding: 22px;
            text-align: center;
        }

        main {
            max-width: 900px;
            margin: 40px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
        }

        .bienvenida {
            color: #8b5e83;
        }

        .opciones {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }

        .opcion {
            display: block;
            padding: 25px;
            background: #f7edf5;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: 0.2s;
        }

        .opcion:hover {
            background: #ead5e5;
            transform: translateY(-3px);
        }

        .salir {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 20px;
            background: #8b5e83;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .salir:hover {
            background: #70466a;
        }
    </style>
</head>

<body>

<header>
    <h1>FashionCloud</h1>
    <p>Panel de Administración</p>
</header>

<main>

    <h2 class="bienvenida">
        ¡Bienvenido/a, <?= $nombre ?>!
    </h2>

    <p>Has ingresado correctamente como administrador.</p>

    <div class="opciones">

        <a class="opcion" href="Productos_admin.php">
            <h3>Productos</h3>
            <p>Gestión de productos.</p>
        </a>

        <a class="opcion" href="Categorias_admin.php">
            <h3>Categorías</h3>
            <p>Gestión de categorías.</p>
        </a>

        <a class="opcion" href="Pedidos_admin.php">
            <h3>Pedidos</h3>
            <p>Consulta de pedidos.</p>
        </a>

        <a class="opcion" href="Usuarios_admin.php">
            <h3>Usuarios</h3>
            <p>Gestión de usuarios.</p>
        </a>

    </div>

    <form action="../routes/authRoutes.php?action=logout" method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
        >

        <button type="submit" class="salir">
            Cerrar sesión
        </button>

    </form>

</main>

</body>
</html>
