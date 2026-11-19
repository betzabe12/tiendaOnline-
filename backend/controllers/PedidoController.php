<?php
class PedidoController {
    public static function crear(PDO $pdo): void {
        if (empty($_SESSION['usuario'])) {
            $_SESSION['flash'] = 'Inicia sesión antes de realizar tu pedido.';
            fc_redirect('frontend/auth.php?modo=login');
        }
        $carrito = $_SESSION['carrito'] ?? [];
        $direccion = is_string($_POST['direccion'] ?? null) ? trim($_POST['direccion']) : '';
        $metodo = $_POST['metodo_pago'] ?? 'por_confirmar';
        $metodosPermitidos = ['por_confirmar', 'transferencia', 'efectivo', 'yape'];

        if (!$carrito || $direccion === '' || strlen($direccion) > 2000 || !in_array($metodo, $metodosPermitidos, true)) {
            $_SESSION['flash'] = 'Revisa el carrito, la dirección y el método de pago.';
            fc_redirect('frontend/carrito.php');
        }

        try {
            $pdo->beginTransaction();
            $productos = [];
            $totalCentavos = 0;
            ksort($carrito, SORT_NUMERIC);
            $stmt = $pdo->prepare('SELECT id, nombre, precio, stock FROM productos WHERE id = ? AND estado = 1 FOR UPDATE');
            foreach ($carrito as $id => $cantidad) {
                $stmt->execute([(int)$id]);
                $p = $stmt->fetch();
                $cantidad = (int)$cantidad;
                if (!$p || $cantidad < 1 || $cantidad > (int)$p['stock']) {
                    throw new RuntimeException('Stock insuficiente o producto no disponible: ' . ($p['nombre'] ?? 'producto'));
                }
                $p['cantidad'] = $cantidad;
                $productos[] = $p;
                $totalCentavos += (int) round((float) $p['precio'] * 100) * $cantidad;
            }

            $total = $totalCentavos / 100;
            $pedidoId = Pedido::crear($pdo, (int) $_SESSION['usuario']['id'], $total, $direccion, $metodo);

            $detalle = $pdo->prepare('INSERT INTO detalle_pedidos (pedido_id,producto_id,cantidad,precio_unitario) VALUES (?,?,?,?)');
            $actualizar = $pdo->prepare('UPDATE productos SET stock = stock - ? WHERE id = ?');
            foreach ($productos as $p) {
                $detalle->execute([$pedidoId, $p['id'], $p['cantidad'], $p['precio']]);
                $actualizar->execute([$p['cantidad'], $p['id']]);
            }
            $pdo->commit();
            unset($_SESSION['carrito']);
            $_SESSION['flash'] = "Pedido #$pedidoId registrado. Total: Bs " . number_format($total, 2);
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $_SESSION['flash'] = $e instanceof RuntimeException && !($e instanceof PDOException) ? $e->getMessage() : 'No se pudo registrar el pedido.';
        }
        fc_redirect('frontend/carrito.php');
    }
}

