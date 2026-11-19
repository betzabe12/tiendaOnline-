<?php
require_once __DIR__ . '/../backend/config/app.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/models/Producto.php';
require_once __DIR__ . '/../backend/models/Pedido.php';
require_once __DIR__ . '/../backend/controllers/PedidoController.php';

$pdo = Database::conectar();
$carrito = $_SESSION['carrito'] ?? [];
$usuario = $_SESSION['usuario'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!fc_verify_csrf()) {
        http_response_code(403);
        exit('La sesión del formulario venció. Recarga la página.');
    }

    $accion = $_POST['accion'] ?? '';
    if ($accion === 'comprar') {
        PedidoController::crear($pdo);
    }

    $id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
    if ($id && $accion === 'quitar') {
        unset($carrito[$id]);
    } elseif ($id && in_array($accion, ['agregar', 'actualizar'], true)) {
        $cantidad = $accion === 'agregar'
            ? ($carrito[$id] ?? 0) + 1
            : filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
        $producto = Producto::buscarPorId($pdo, $id);
        if (!$producto || !$cantidad || $cantidad < 1 || $cantidad > (int) $producto['stock']) {
            fc_set_flash('La cantidad solicitada supera el stock disponible o la prenda ya no está disponible.', 'error');
        } else {
            $carrito[$id] = $cantidad;
        }
    }
    $_SESSION['carrito'] = $carrito;
    fc_redirect('frontend/carrito.php');
}

$productos = [];
$total = 0;
$disponible = true;
foreach ($carrito as $id => $cantidad) {
    $producto = Producto::buscarPorId($pdo, $id);
    $puedeComprar = $producto && $cantidad <= (int) $producto['stock'];
    $subtotal = $producto ? $producto['precio'] * $cantidad : 0;
    $total += $subtotal;
    $disponible = $disponible && $puedeComprar;
    $productos[] = compact('id', 'cantidad', 'producto', 'subtotal', 'puedeComprar');
}
$pedidos = $usuario ? Pedido::listarPorUsuario($pdo, (int) $usuario['id']) : [];
$mensaje = $_SESSION['flash'] ?? '';
unset($_SESSION['flash'], $_SESSION['flash_type']);
$token = fc_csrf_token();
function h($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carrito | FashionCloud</title>
    <link rel="stylesheet" href="css/compras.css">
</head>
<body>
<main>
    <a href="index.php#coleccion">← Volver a la tienda</a>
    <h1>Carrito</h1>
    <?php if ($mensaje !== ''): ?><p role="status"><?= h($mensaje) ?></p><?php endif; ?>
    <?php if (!$productos): ?>
        <p>Tu carrito está vacío.</p>
    <?php else: ?>
        <?php foreach ($productos as $fila): ?>
            <section>
                <h2><?= h($fila['producto']['nombre'] ?? 'Prenda no disponible') ?></h2>
                <p>Subtotal: Bs <?= number_format($fila['subtotal'], 2) ?></p>
                <?php if (!$fila['puedeComprar']): ?><p>Revisa la cantidad o retira esta prenda.</p><?php endif; ?>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= h($token) ?>">
                    <input type="hidden" name="producto_id" value="<?= (int) $fila['id'] ?>">
                    <label>Cantidad
                        <input type="number" name="cantidad" min="1" value="<?= (int) $fila['cantidad'] ?>">
                    </label>
                    <button name="accion" value="actualizar" <?= !$fila['producto'] ? 'disabled' : '' ?>>Actualizar</button>
                    <button name="accion" value="quitar" formnovalidate>Quitar</button>
                </form>
            </section>
        <?php endforeach; ?>
        <p><strong>Total: Bs <?= number_format($total, 2) ?></strong></p>
        <?php if (!$usuario): ?>
            <a href="auth.php?modo=login">Inicia sesión para comprar</a>
        <?php elseif ($disponible): ?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= h($token) ?>">
                <label>Dirección de entrega <input name="direccion" required maxlength="500"></label>
                <label>Método de pago
                    <select name="metodo_pago" required>
                        <option value="">Elige una opción</option>
                        <option value="efectivo">Efectivo contra entrega</option>
                        <option value="yape">Yape por QR</option>
                        <option value="transferencia">Transferencia bancaria</option>
                    </select>
                </label>
                <p>Para pagar por transferencia, coordina los datos bancarios con la tienda. La confirmación es manual.</p>
                <button name="accion" value="comprar">Confirmar pedido</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($usuario): ?>
        <h2>Mis pedidos</h2>
        <?php if (!$pedidos): ?><p>Todavía no tienes pedidos.</p><?php endif; ?>
        <?php foreach ($pedidos as $pedido): ?>
            <p>#<?= (int) $pedido['id'] ?> · <?= h(ucfirst($pedido['estado'])) ?> · Bs <?= number_format((float) $pedido['total'], 2) ?> · <?= h(ucfirst(str_replace('_', ' ', $pedido['metodo_pago']))) ?></p>
            <?php if ($pedido['metodo_pago'] === 'yape' && $pedido['estado'] === 'pendiente'): ?>
                <section>
                    <h3>Paga el pedido #<?= (int) $pedido['id'] ?> por Yape</h3>
                    <p>Escanea el QR e ingresa <strong>Bs <?= number_format((float) $pedido['total'], 2, '.', '') ?></strong>. La tienda verificará el pago para confirmar tu pedido.</p>
                    <img src="img/yape-qr.jpeg" width="280" style="max-width: 100%; height: auto" alt="Código QR de Yape">
                    <p><a href="img/yape-qr.jpeg" download>Descargar QR</a></p>
                </section>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
</body>
</html>
