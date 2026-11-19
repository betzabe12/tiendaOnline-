
<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Producto.php';

if (empty($_SESSION['usuario'])) {
    fc_redirect('frontend/auth.php');
}

if (($_SESSION['usuario']['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

$pdo = Database::conectar();

$categorias = Categoria::listar($pdo);
$productos = Producto::listar($pdo);

$nombreAdmin = htmlspecialchars(
    $_SESSION['usuario']['nombre'] ?? 'Administrador',
    ENT_QUOTES,
    'UTF-8'
);

$flash = $_SESSION['flash'] ?? '';
$flashType = $_SESSION['flash_type'] ?? 'info';
unset($_SESSION['flash'], $_SESSION['flash_type']);

function escapar($valor): string
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar productos | FashionCloud</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 25px;
            font-family: Arial, sans-serif;
            background: #f4f5f9;
            color: #252538;
        }

        .contenedor {
            max-width: 1200px;
            margin: auto;
        }

        .encabezado {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        h1 {
            margin: 0 0 8px;
        }

        .subtitulo {
            color: #666;
            margin: 0;
        }

        .boton {
            display: inline-block;
            padding: 11px 17px;
            border: 0;
            border-radius: 7px;
            background: #282845;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .tarjeta {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 3px 12px rgba(0,0,0,.05);
        }

        .formulario {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .campo {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .campo.completo {
            grid-column: 1 / -1;
        }

        label {
            font-weight: bold;
            font-size: 14px;
        }

        input, textarea, select {
            width: 100%;
            padding: 11px;
            border: 1px solid #d5d5df;
            border-radius: 7px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 85px;
        }

        .guardar {
            background: #287a52;
            grid-column: 1 / -1;
            justify-self: start;
        }

        .mensaje {
            padding: 13px;
            border-radius: 7px;
            margin-bottom: 20px;
            background: #e9f3ff;
            color: #17436d;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th, td {
            padding: 13px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f7f7fa;
        }

        .eliminar {
            background: #b83232;
        }

        .cantidad {
            color: #666;
            margin-bottom: 18px;
        }

        @media (max-width: 650px) {
            body {
                padding: 12px;
            }

            .formulario {
                grid-template-columns: 1fr;
            }

            .campo.completo,
            .guardar {
                grid-column: 1;
            }
        }
    </style>
</head>

<body>
<div class="contenedor">

    <header class="encabezado">
        <div>
            <h1>Administración de productos</h1>
            <p class="subtitulo">Bienvenido, <?= $nombreAdmin ?>.</p>
        </div>

        <a class="boton"
           href="<?= escapar(fc_url('backend/admin/Panel_admin.php')) ?>">
            Volver al panel
        </a>
    </header>

    <?php if ($flash !== ''): ?>
        <div class="mensaje">
            <?= escapar($flash) ?>
        </div>
    <?php endif; ?>

    <section class="tarjeta">
        <h2>Registrar nuevo producto</h2>

        <form class="formulario"
              action="<?= escapar(fc_url('backend/routes/productoRoutes.php?action=crear')) ?>"
              method="POST">

            <input type="hidden" name="csrf_token"
                   value="<?= escapar(fc_csrf_token()) ?>">

            <div class="campo">
                <label for="codigo">Código del producto *</label>
                <input type="text" id="codigo" name="codigo" required>
            </div>

            <div class="campo">
                <label for="nombre">Nombre del producto *</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="campo completo">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion"></textarea>
            </div>

            <div class="campo">
                <label for="precio">Precio *</label>
                <input type="number" id="precio" name="precio"
                       min="0" step="0.01" required>
            </div>

            <div class="campo">
                <label for="stock">Stock *</label>
                <input type="number" id="stock" name="stock"
                       min="0" step="1" required>
            </div>

            <div class="campo">
                <label for="talla">Talla</label>
                <input type="text" id="talla" name="talla">
            </div>

            <div class="campo">
                <label for="color">Color</label>
                <input type="text" id="color" name="color">
            </div>

            <div class="campo">
                <label for="imagen_url">URL de imagen</label>
                <input type="url" id="imagen_url" name="imagen_url">
            </div>

            <div class="campo">
                <label for="categoria_id">Categoría</label>
                <select id="categoria_id" name="categoria_id">
                    <option value="">Sin categoría</option>

                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= (int)$categoria['id'] ?>">
                            <?= escapar($categoria['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="boton guardar" type="submit">
                Registrar producto
            </button>
        </form>
    </section>

    <section class="tarjeta">
        <h2>Productos registrados</h2>

        <p class="cantidad">
            Total de productos activos: <?= count($productos) ?>
        </p>

        <div class="tabla-contenedor">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Talla</th>
                        <th>Color</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="8">
                                Todavía no hay productos registrados.
                            </td>
                        </tr>
                    <?php else: ?>

                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?= (int)$producto['id'] ?></td>
                                <td><?= escapar($producto['codigo']) ?></td>
                                <td><?= escapar($producto['nombre']) ?></td>
                                <td><?= number_format((float)$producto['precio'], 2) ?></td>
                                <td><?= (int)$producto['stock'] ?></td>
                                <td><?= escapar($producto['talla']) ?></td>
                                <td><?= escapar($producto['color']) ?></td>

                                <td>
                                    <form method="POST"
                                          action="<?= escapar(fc_url('backend/routes/productoRoutes.php?action=eliminar')) ?>"
                                          onsubmit="return confirm('¿Seguro que deseas desactivar este producto?');">

                                        <input type="hidden" name="csrf_token"
                                               value="<?= escapar(fc_csrf_token()) ?>">

                                        <input type="hidden" name="id"
                                               value="<?= (int)$producto['id'] ?>">

                                        <button type="submit"
                                                class="boton eliminar">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</div>
</body>
</html>
