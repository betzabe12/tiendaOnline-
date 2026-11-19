<?php
require_once __DIR__ . '/_compras.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Usuario.php';

$transiciones = [
    'pendiente' => ['confirmado', 'cancelado'],
    'confirmado' => ['enviado', 'cancelado'],
    'enviado' => ['entregado'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $nuevo = $_POST['estado'] ?? '';
    $mensaje = 'El pedido ya cambió de estado o la acción no es válida.';
    try {
        $pdo->beginTransaction();
        $consulta = $pdo->prepare('SELECT estado FROM pedidos WHERE id = ? FOR UPDATE');
        $consulta->execute([$id]);
        $actual = $consulta->fetchColumn();
        if ($actual && in_array($nuevo, $transiciones[$actual] ?? [], true)) {
            if ($nuevo === 'cancelado') {
                $detalle = $pdo->prepare('SELECT producto_id, cantidad FROM detalle_pedidos WHERE pedido_id = ? ORDER BY producto_id');
                $detalle->execute([$id]);
                $stock = $pdo->prepare('UPDATE productos SET stock = stock + ? WHERE id = ?');
                foreach ($detalle->fetchAll() as $producto) {
                    $stock->execute([$producto['cantidad'], $producto['producto_id']]);
                }
            }
            Pedido::actualizarEstado($pdo, $id, $nuevo);
            $mensaje = 'Estado del pedido actualizado.';
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log($e->getMessage());
        $mensaje = 'No se pudo actualizar el pedido.';
    }
    fc_set_flash($mensaje);
    fc_redirect('backend/admin/Pedidos_admin.php');
}

$pedidos = Pedido::listarTodos($pdo);
$clientes = array_column(Usuario::listar($pdo), 'nombre', 'id');
$detalle = $pdo->prepare('SELECT p.nombre, d.cantidad, d.precio_unitario
    FROM detalle_pedidos d JOIN productos p ON p.id = d.producto_id
    WHERE d.pedido_id = ? ORDER BY d.id');
admin_inicio('Pedidos');
?>
<?php if (!$pedidos): ?><p>Todavía no hay pedidos.</p><?php endif; ?>
<?php foreach ($pedidos as $pedido): ?>
    <section>
        <h2>Pedido #<?= (int) $pedido['id'] ?> · <?= admin_h(ucfirst($pedido['estado'])) ?></h2>
        <p><?= admin_h($clientes[$pedido['usuario_id']] ?? '') ?> · <?= admin_h($pedido['direccion']) ?></p>
        <p>Bs <?= number_format((float) $pedido['total'], 2) ?> · <?= admin_h(ucfirst(str_replace('_', ' ', $pedido['metodo_pago']))) ?></p>
        <?php $detalle->execute([$pedido['id']]); ?>
        <ul>
            <?php foreach ($detalle->fetchAll() as $prenda): ?>
                <li><?= (int) $prenda['cantidad'] ?> × <?= admin_h($prenda['nombre']) ?> · Bs <?= number_format((float) $prenda['precio_unitario'], 2) ?> c/u</li>
            <?php endforeach; ?>
        </ul>
        <?php if ($pedido['estado'] === 'pendiente' && in_array($pedido['metodo_pago'], ['yape', 'transferencia'], true)): ?>
            <p>Verifica el ingreso del pago antes de confirmar el pedido.</p>
        <?php endif; ?>
        <?php if (isset($transiciones[$pedido['estado']])): ?>
            <form method="post">
                <?php admin_token(); ?>
                <input type="hidden" name="id" value="<?= (int) $pedido['id'] ?>">
                <label>Nuevo estado
                    <select name="estado" required>
                        <?php foreach ($transiciones[$pedido['estado']] as $estado): ?>
                            <option value="<?= admin_h($estado) ?>"><?= admin_h(ucfirst($estado)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button>Actualizar pedido</button>
            </form>
        <?php endif; ?>
    </section>
<?php endforeach; ?>
</main>
</body>
</html>
