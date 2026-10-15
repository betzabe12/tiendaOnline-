<?php
class PedidoController {
    public static function crear(PDO $pdo): void {
        if (empty($_SESSION['usuario'])) {
            $_SESSION['flash'] = 'Inicia sesión antes de realizar tu pedido.';
            header('Location: ../views/login.php');
            exit;
        }
        $carrito = $_SESSION['carrito'] ?? [];
        $direccion = trim($_POST['direccion'] ?? '');
        $metodo = $_POST['metodo_pago'] ?? 'por_confirmar';
        $metodosPermitidos = ['por_confirmar', 'transferencia', 'efectivo'];

        if (!$carrito || $direccion === '' || !in_array($metodo, $metodosPermitidos, true)) {
            $_SESSION['flash'] = 'Revisa el carrito, la dirección y el método de pago.';
            header('Location: ../views/carrito.php');
            exit;
        }

        try {
            $pdo->beginTransaction();
            $productos = [];
            $total = 0.0;
            foreach ($carrito as $id => $cantidad) {
                $stmt = $pdo->prepare('SELECT id, nombre, precio, stock FROM productos WHERE id = ? AND estado = 1 FOR UPDATE');
                $stmt->execute([(int)$id]);
                $p = $stmt->fetch();
                $cantidad = (int)$cantidad;
                if (!$p || $cantidad < 1 || $cantidad > (int)$p['stock']) {
                    throw new RuntimeException('Stock insuficiente o producto no disponible: ' . ($p['nombre'] ?? 'producto'));
                }
                $p['cantidad'] = $cantidad;
                $productos[] = $p;
                $total += (float)$p['precio'] * $cantidad;
            }

            $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id,total,direccion,estado,metodo_pago) VALUES (?,?,?,'pendiente',?)");
            $stmt->execute([$_SESSION['usuario']['id'], $total, $direccion, $metodo]);
            $pedidoId = (int)$pdo->lastInsertId();

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
            $_SESSION['flash'] = $e instanceof RuntimeException ? $e->getMessage() : 'No se pudo registrar el pedido.';
        }
        header('Location: ../views/pedidos.php');
        exit;
    }
}

