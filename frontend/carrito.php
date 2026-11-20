<?php
require_once __DIR__ . '/../backend/config/app.php';
require_once __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/models/Producto.php';
require_once __DIR__ . '/../backend/models/Pedido.php';
require_once __DIR__ . '/../backend/controllers/PedidoController.php';

$pdo = Database::conectar();
$usuario = $_SESSION['usuario'] ?? null;

function fc_carrito_clave(?array $usuario): string
{
    if (is_array($usuario) && !empty($usuario['id'])) {
        return 'usuario_' . (int) $usuario['id'];
    }
    return 'invitado';
}

$claveCarrito = fc_carrito_clave($usuario);
$_SESSION['carritos'] = is_array($_SESSION['carritos'] ?? null) ? $_SESSION['carritos'] : [];

// Si existía un carrito global previo o de invitado y ahora hay usuario, migrar sin perder prendas
if ($claveCarrito !== 'invitado') {
    $_SESSION['carritos'][$claveCarrito] = is_array($_SESSION['carritos'][$claveCarrito] ?? null)
        ? $_SESSION['carritos'][$claveCarrito]
        : [];

    foreach (['invitado', 'global_legacy'] as $origen) {
        if ($origen === 'global_legacy' && !empty($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $prodId => $cant) {
                $prodId = (int) $prodId;
                $cant = (int) $cant;
                if ($prodId > 0 && $cant > 0) {
                    $_SESSION['carritos'][$claveCarrito][$prodId] = ($_SESSION['carritos'][$claveCarrito][$prodId] ?? 0) + $cant;
                }
            }
            unset($_SESSION['carrito']);
        } elseif (!empty($_SESSION['carritos'][$origen]) && is_array($_SESSION['carritos'][$origen])) {
            foreach ($_SESSION['carritos'][$origen] as $prodId => $cant) {
                $prodId = (int) $prodId;
                $cant = (int) $cant;
                if ($prodId > 0 && $cant > 0) {
                    $_SESSION['carritos'][$claveCarrito][$prodId] = ($_SESSION['carritos'][$claveCarrito][$prodId] ?? 0) + $cant;
                }
            }
            unset($_SESSION['carritos'][$origen]);
        }
    }
} elseif (!isset($_SESSION['carritos']['invitado']) && !empty($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    $_SESSION['carritos']['invitado'] = $_SESSION['carrito'];
}

$carrito = is_array($_SESSION['carritos'][$claveCarrito] ?? null) ? $_SESSION['carritos'][$claveCarrito] : [];
// Sincronizar $_SESSION['carrito'] con el carrito de la persona activa por si PedidoController lo consulta
$_SESSION['carrito'] = $carrito;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!fc_verify_csrf()) {
        http_response_code(403);
        exit('La sesión del formulario venció. Recarga la página.');
    }

    $accion = $_POST['accion'] ?? '';

    if ($accion === 'comprar') {
        $_SESSION['carritos'][$claveCarrito] = $carrito;
        $_SESSION['carrito'] = $carrito;

        try {
            PedidoController::crear($pdo);
        } catch (Throwable $e) {
            error_log('FashionCloud Carrito/Pedido: ' . $e->getMessage());
            fc_set_flash('No se pudo procesar tu pedido. Inténtalo nuevamente.', 'error');
            fc_redirect('frontend/carrito.php');
        }

        // Si PedidoController::crear() no redirigió ni limpió el carrito, limpiamos el carrito de esta persona
        unset($_SESSION['carritos'][$claveCarrito], $_SESSION['carrito']);
        fc_redirect('frontend/carrito.php#mis-pedidos');
    }

    $id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
    if ($id && $accion === 'quitar') {
        unset($carrito[$id]);
    } elseif ($id && in_array($accion, ['agregar', 'actualizar'], true)) {
        $cantidad = $accion === 'agregar'
            ? ((int) ($carrito[$id] ?? 0)) + 1
            : filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);

        $producto = Producto::buscarPorId($pdo, $id);
        if (!$producto || !$cantidad || $cantidad < 1 || $cantidad > (int) $producto['stock']) {
            fc_set_flash('La cantidad solicitada supera el stock disponible o la prenda ya no está disponible.', 'error');
        } else {
            $carrito[$id] = $cantidad;
        }
    }

    $_SESSION['carritos'][$claveCarrito] = $carrito;
    $_SESSION['carrito'] = $carrito;
    fc_redirect('frontend/carrito.php');
}

$productos = [];
$total = 0.0;
$disponible = true;

foreach ($carrito as $id => $cantidad) {
    $idEntero = (int) $id;
    $cantidadEntera = (int) $cantidad;
    $producto = $idEntero > 0 ? Producto::buscarPorId($pdo, $idEntero) : null;
    $puedeComprar = is_array($producto) && $cantidadEntera >= 1 && $cantidadEntera <= (int) $producto['stock'];
    $subtotal = is_array($producto) ? ((float) $producto['precio']) * $cantidadEntera : 0.0;
    $total += $subtotal;
    $disponible = $disponible && $puedeComprar;

    $productos[] = [
        'id' => $idEntero,
        'cantidad' => $cantidadEntera,
        'producto' => $producto,
        'subtotal' => $subtotal,
        'puedeComprar' => $puedeComprar,
    ];
}

$pedidos = [];
if (is_array($usuario) && !empty($usuario['id'])) {
    try {
        $pedidos = Pedido::listarPorUsuario($pdo, (int) $usuario['id']);
        if (!is_array($pedidos)) {
            $pedidos = [];
        }
    } catch (Throwable $e) {
        error_log('FashionCloud Listar Pedidos: ' . $e->getMessage());
        $pedidos = [];
    }
}

$mensaje = $_SESSION['flash'] ?? '';
$flashType = $_SESSION['flash_type'] ?? 'info';
unset($_SESSION['flash'], $_SESSION['flash_type']);
$token = fc_csrf_token();

$cssFile = file_exists(__DIR__ . '/css/compras.css') ? 'frontend/css/compras.css' : 'frontend/css/Compras.css';

function h($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function formato_metodo(?string $metodo): string
{
    $limpio = trim((string) $metodo);
    if ($limpio === '') {
        return '';
    }
    return ucfirst(str_replace('_', ' ', $limpio));
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Carrito y Pedidos | FashionCloud</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= h(fc_url($cssFile)) ?>">
</head>
<body>
<main class="fc-shell">
    <header class="fc-topbar">
        <a class="fc-back-link" href="index.php#coleccion">← Volver a la tienda</a>
        <a class="fc-brand" href="index.php">FashionCloud<span>.</span></a>
    </header>

    <div class="fc-page-head">
        <span class="fc-eyebrow">Boutique · Compras</span>
        <h1>Tu Carrito</h1>
        <p>Revisa tus prendas seleccionadas y consulta el estado de tus pedidos.</p>
    </div>

    <?php if ($mensaje !== ''): ?>
        <div class="fc-alert <?= $flashType === 'error' ? 'fc-alert--error' : 'fc-alert--info' ?>" role="status">
            <span><?= $flashType === 'error' ? '!' : '✓' ?></span>
            <span><?= h($mensaje) ?></span>
        </div>
    <?php endif; ?>

    <section class="fc-panel" aria-label="Artículos en el carrito">
        <?php if (!$productos): ?>
            <div class="fc-empty-state">
                <?php if ($usuario && !empty($pedidos)): ?>
                    <div class="fc-empty-badge" aria-hidden="true">✦</div>
                    <p class="fc-empty-title">No tienes prendas en el carrito ahora mismo.</p>
                    <p class="fc-empty-sub">Ya realizaste pedidos anteriormente: revisa tus compras en <strong>Mis pedidos</strong> o explora nuevas prendas.</p>
                    <div class="fc-empty-actions">
                        <a class="fc-btn fc-btn--primary" href="#mis-pedidos">Leer mis pedidos (<?= count($pedidos) ?>)</a>
                        <a class="fc-btn fc-btn--ghost" href="index.php#coleccion">Seguir comprando</a>
                    </div>
                <?php else: ?>
                    <div class="fc-empty-badge" aria-hidden="true">♡</div>
                    <p class="fc-empty-title">Tu carrito está vacío.</p>
                    <p class="fc-empty-sub">Explora el catálogo y agrega las prendas que más te gusten.</p>
                    <div class="fc-empty-actions">
                        <a class="fc-btn fc-btn--primary" href="index.php#coleccion">Ir al catálogo</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="fc-items-list">
                <?php foreach ($productos as $fila): ?>
                    <?php $prod = $fila['producto']; ?>
                    <article class="fc-item-card">
                        <div class="fc-item-info">
                            <h2><?= h(is_array($prod) ? ($prod['nombre'] ?? 'Prenda no disponible') : 'Prenda no disponible') ?></h2>
                            <?php if (is_array($prod)): ?>
                                <p class="fc-item-meta">
                                    <span>Unitario: <strong>Bs <?= number_format((float) $prod['precio'], 2) ?></strong></span>
                                    <?php if (!empty($prod['talla'])): ?>
                                        <span>· Talla: <strong><?= h($prod['talla']) ?></strong></span>
                                    <?php endif; ?>
                                    <?php if (!empty($prod['color'])): ?>
                                        <span>· Color: <strong><?= h($prod['color']) ?></strong></span>
                                    <?php endif; ?>
                                    <span>· Stock: <strong><?= (int) $prod['stock'] ?></strong></span>
                                </p>
                            <?php endif; ?>
                            <p class="fc-item-subtotal">Subtotal: <strong>Bs <?= number_format($fila['subtotal'], 2) ?></strong></p>
                            <?php if (!$fila['puedeComprar']): ?>
                                <p class="fc-warning">Revisa la cantidad o retira esta prenda para continuar.</p>
                            <?php endif; ?>
                        </div>

                        <form class="fc-item-form" method="post">
                            <input type="hidden" name="csrf_token" value="<?= h($token) ?>">
                            <input type="hidden" name="producto_id" value="<?= (int) $fila['id'] ?>">
                            <label class="fc-inline-label">
                                Cantidad
                                <input type="number" name="cantidad" min="1" max="<?= is_array($prod) ? (int) $prod['stock'] : 99 ?>" value="<?= (int) $fila['cantidad'] ?>">
                            </label>
                            <div class="fc-form-actions">
                                <button class="fc-btn fc-btn--secondary fc-btn--sm" name="accion" value="actualizar" <?= !is_array($prod) ? 'disabled' : '' ?>>
                                    Actualizar
                                </button>
                                <button class="fc-btn fc-btn--danger fc-btn--sm" name="accion" value="quitar" formnovalidate>
                                    Quitar
                                </button>
                            </div>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="fc-summary-box">
                <div class="fc-total-row">
                    <span>Total a pagar</span>
                    <strong>Bs <?= number_format($total, 2) ?></strong>
                </div>

                <?php if (!$usuario): ?>
                    <a class="fc-btn fc-btn--primary" href="auth.php?modo=login">Inicia sesión para comprar</a>
                <?php elseif ($disponible): ?>
                    <form class="fc-checkout-form" method="post">
                        <input type="hidden" name="csrf_token" value="<?= h($token) ?>">
                        <div class="fc-field">
                            <label for="direccion">Dirección de entrega</label>
                            <input id="direccion" name="direccion" placeholder="Ej. Calle / Zona, referencia..." required maxlength="500">
                        </div>
                        <div class="fc-field">
                            <label for="metodo_pago">Método de pago</label>
                            <select id="metodo_pago" name="metodo_pago" required>
                                <option value="">Elige una opción</option>
                                <option value="efectivo">Efectivo contra entrega</option>
                                <option value="yape">Yape por QR</option>
                                <option value="transferencia">Transferencia bancaria</option>
                            </select>
                        </div>
                        <p class="fc-note">
                            Para pagar por transferencia o Yape, realiza el pago y espera la verificación manual de la tienda.
                        </p>
                        <button class="fc-btn fc-btn--gold" name="accion" value="comprar">Confirmar pedido</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($usuario): ?>
        <section class="fc-panel fc-orders-section" id="mis-pedidos" aria-labelledby="titulo-mis-pedidos">
            <div class="fc-section-head">
                <h2 id="titulo-mis-pedidos">Mis pedidos</h2>
                <span class="fc-pill"><?= count($pedidos) ?> <?= count($pedidos) === 1 ? 'pedido' : 'pedidos' ?></span>
            </div>

            <?php if (!$pedidos): ?>
                <p class="fc-muted">Todavía no tienes pedidos.</p>
            <?php else: ?>
                <div class="fc-orders-list">
                    <?php foreach ($pedidos as $pedido): ?>
                        <?php
                        $pedidoId = (int) ($pedido['id'] ?? 0);
                        $estadoRaw = strtolower((string) ($pedido['estado'] ?? 'pendiente'));
                        $metodoRaw = strtolower((string) ($pedido['metodo_pago'] ?? ''));
                        $metodoTxt = formato_metodo($pedido['metodo_pago'] ?? '');
                        ?>
                        <article class="fc-order-card">
                            <div class="fc-order-header">
                                <div class="fc-order-main">
                                    <span class="fc-order-id">#<?= $pedidoId ?></span>
                                    <span class="fc-status fc-status--<?= h($estadoRaw) ?>">
                                        <?= h(ucfirst($estadoRaw)) ?>
                                    </span>
                                    <span class="fc-order-amount">Bs <?= number_format((float) ($pedido['total'] ?? 0), 2) ?></span>
                                    <?php if ($metodoTxt !== ''): ?>
                                        <span class="fc-order-method">· <?= h($metodoTxt) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($pedido['direccion'])): ?>
                                    <p class="fc-order-address">Entrega: <?= h($pedido['direccion']) ?></p>
                                <?php endif; ?>
                                <div class="fc-order-actions" style="margin-top: 0.75rem;">
                                    <a class="fc-btn fc-btn--secondary fc-btn--sm" href="<?= h(fc_url('frontend/factura.php?pedido_id=' . $pedidoId)) ?>">
                                        Ver / descargar factura
                                    </a>
                                </div>
                            </div>

                            <?php if ($estadoRaw === 'pendiente' && in_array($metodoRaw, ['yape', 'transferencia'], true)): ?>
                                <div class="fc-payment-box">
                                    <h3>Pago del pedido #<?= $pedidoId ?></h3>
                                    <?php if ($metodoRaw === 'yape'): ?>
                                        <p>Escanea el QR e ingresa <strong>Bs <?= number_format((float) ($pedido['total'] ?? 0), 2, '.', '') ?></strong>. La tienda verificará el ingreso para confirmar tu pedido.</p>
                                        <div class="fc-qr-wrap">
                                            <img src="img/yape-qr.jpeg" width="220" alt="Código QR de Yape">
                                            <a class="fc-btn fc-btn--ghost fc-btn--sm" href="img/yape-qr.jpeg" download>Descargar QR</a>
                                        </div>
                                    <?php else: ?>
                                        <p>Coordina los datos de transferencia con la tienda e indica el número de pedido <strong>#<?= $pedidoId ?></strong>.</p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>
</body>
</html>