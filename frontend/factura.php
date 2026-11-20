<?php
require_once __DIR__ . '/../backend/config/app.php';
require_once __DIR__ . '/../backend/config/database.php';

if (empty($_SESSION['usuario'])) {
    fc_redirect('frontend/auth.php?modo=login');
}

$pedidoId = filter_input(INPUT_GET, 'pedido_id', FILTER_VALIDATE_INT);
if (!$pedidoId || $pedidoId < 1) {
    fc_set_flash('Selecciona un pedido válido para generar su factura.', 'error');
    fc_redirect('frontend/carrito.php#mis-pedidos');
}

$pdo = Database::conectar();
$usuarioSesion = $_SESSION['usuario'];
$esAdmin = (($usuarioSesion['rol'] ?? '') === 'admin');

$sqlPedido = 'SELECT p.*, u.nombre AS cliente_nombre, u.correo AS cliente_correo
              FROM pedidos p
              INNER JOIN usuarios u ON u.id = p.usuario_id
              WHERE p.id = ?';
$params = [$pedidoId];

if (!$esAdmin) {
    $sqlPedido .= ' AND p.usuario_id = ?';
    $params[] = (int) $usuarioSesion['id'];
}

$stmtPedido = $pdo->prepare($sqlPedido);
$stmtPedido->execute($params);
$pedido = $stmtPedido->fetch();

if (!$pedido) {
    fc_set_flash('No se encontró el pedido solicitado o no tienes permiso para verlo.', 'error');
    fc_redirect($esAdmin ? 'backend/admin/Pedidos_admin.php' : 'frontend/carrito.php#mis-pedidos');
}

$stmtDetalle = $pdo->prepare(
    'SELECT d.cantidad, d.precio_unitario,
            pr.codigo AS producto_codigo,
            pr.nombre AS producto_nombre,
            pr.talla AS producto_talla,
            pr.color AS producto_color
     FROM detalle_pedidos d
     INNER JOIN productos pr ON pr.id = d.producto_id
     WHERE d.pedido_id = ?
     ORDER BY d.id ASC'
);
$stmtDetalle->execute([$pedidoId]);
$items = $stmtDetalle->fetchAll();

function fh($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

$codigoFactura = !empty($pedido['numero_factura'])
    ? (string) $pedido['numero_factura']
    : sprintf('FC-%06d', (int) $pedido['id']);

$fechaEmision = !empty($pedido['fecha_creacion'])
    ? (string) $pedido['fecha_creacion']
    : (!empty($pedido['created_at']) ? (string) $pedido['created_at'] : date('Y-m-d H:i'));

$metodoPagoRaw = trim((string) ($pedido['metodo_pago'] ?? ''));
$metodoPagoTexto = $metodoPagoRaw !== '' ? ucfirst(str_replace('_', ' ', $metodoPagoRaw)) : 'Por confirmar';
$estadoTexto = ucfirst(strtolower((string) ($pedido['estado'] ?? 'pendiente')));

$subtotalCalculado = 0.0;
foreach ($items as $item) {
    $subtotalCalculado += (int) $item['cantidad'] * (float) $item['precio_unitario'];
}
$totalFactura = isset($pedido['total']) ? (float) $pedido['total'] : $subtotalCalculado;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Factura <?= fh($codigoFactura) ?> | FashionCloud</title>
    <link rel="stylesheet" href="<?= fh(fc_url('frontend/css/factura.css')) ?>">
</head>
<body>
<div class="inv-page">
    <div class="inv-toolbar no-print">
        <a class="inv-btn inv-btn--ghost" href="<?= fh(fc_url($esAdmin ? 'backend/admin/Pedidos_admin.php' : 'frontend/carrito.php#mis-pedidos')) ?>">
            ← Volver a pedidos
        </a>
        <div class="inv-toolbar-actions">
            <button type="button" class="inv-btn inv-btn--gold" onclick="window.print()">
                Imprimir / Guardar PDF
            </button>
        </div>
    </div>

    <article class="inv-sheet">
        <header class="inv-header">
            <div>
                <div class="inv-brand">FashionCloud<span>.</span></div>
                <p class="inv-tagline">Boutique de Moda Contemporánea · Comprobante de Compra</p>
            </div>
            <div class="inv-doc-meta">
                <span class="inv-badge">Factura / Nota de Venta</span>
                <div class="inv-number"><?= fh($codigoFactura) ?></div>
                <div class="inv-submeta">Pedido #<?= (int) $pedido['id'] ?> · Estado: <strong><?= fh($estadoTexto) ?></strong></div>
                <div class="inv-submeta">Fecha: <?= fh($fechaEmision) ?></div>
            </div>
        </header>

        <section class="inv-parties">
            <div class="inv-party-box">
                <h2>Emisor</h2>
                <p><strong>FashionCloud</strong></p>
                <p>Tienda Online de Moda</p>
                <p>Moneda: Bolivianos (Bs)</p>
            </div>
            <div class="inv-party-box">
                <h2>Datos del cliente</h2>
                <p><strong><?= fh($pedido['cliente_nombre']) ?></strong></p>
                <p><?= fh($pedido['cliente_correo']) ?></p>
                <p><strong>Entrega:</strong> <?= fh($pedido['direccion'] ?: 'No especificada') ?></p>
                <p><strong>Método de pago:</strong> <?= fh($metodoPagoTexto) ?></p>
            </div>
        </section>

        <section class="inv-table-wrap" aria-label="Detalle de prendas">
            <table class="inv-table">
                <thead>
                    <tr>
                        <th>Prenda</th>
                        <th>Detalle</th>
                        <th class="num">Cant.</th>
                        <th class="num">P. Unitario</th>
                        <th class="num">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$items): ?>
                        <tr>
                            <td colspan="5" class="inv-empty">No hay líneas de detalle registradas para este pedido.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <?php
                            $cant = (int) $item['cantidad'];
                            $precioUnit = (float) $item['precio_unitario'];
                            $lineaSubtotal = $cant * $precioUnit;
                            $specs = array_filter([
                                !empty($item['producto_codigo']) ? 'Cód: ' . $item['producto_codigo'] : '',
                                !empty($item['producto_talla']) ? 'Talla: ' . $item['producto_talla'] : '',
                                !empty($item['producto_color']) ? 'Color: ' . $item['producto_color'] : '',
                            ]);
                            ?>
                            <tr>
                                <td><strong><?= fh($item['producto_nombre']) ?></strong></td>
                                <td class="inv-specs"><?= fh(implode(' · ', $specs) ?: '—') ?></td>
                                <td class="num"><?= $cant ?></td>
                                <td class="num">Bs <?= number_format($precioUnit, 2) ?></td>
                                <td class="num"><strong>Bs <?= number_format($lineaSubtotal, 2) ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <footer class="inv-footer">
            <div class="inv-notes">
                <p><strong>Nota:</strong> Este documento resume el detalle de tu compra registrada en FashionCloud.</p>
                <?php if (strtolower((string) ($pedido['estado'] ?? '')) === 'pendiente'): ?>
                    <p>Si elegiste Yape o transferencia, tu pedido se confirmará tras verificar el pago.</p>
                <?php endif; ?>
            </div>
            <div class="inv-totals">
                <div class="inv-total-line">
                    <span>Subtotal ítems</span>
                    <span>Bs <?= number_format($subtotalCalculado, 2) ?></span>
                </div>
                <div class="inv-total-line inv-total-grand">
                    <span>Total pagado / a pagar</span>
                    <strong>Bs <?= number_format($totalFactura, 2) ?></strong>
                </div>
            </div>
        </footer>
    </article>
</div>
</body>
</html>