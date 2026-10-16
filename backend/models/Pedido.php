<?php

class Pedido
{
    // Registrar un pedido
    public static function crear(
        PDO $pdo,
        int $usuarioId,
        float $total,
        string $direccion,
        string $metodoPago
    ): int {
        $sql = "INSERT INTO pedidos
                (usuario_id, total, direccion, estado, metodo_pago)
                VALUES (?, ?, ?, 'pendiente', ?)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $usuarioId,
            $total,
            $direccion,
            $metodoPago
        ]);

        return (int) $pdo->lastInsertId();
    }

    // Buscar un pedido por su ID
    public static function buscarPorId(PDO $pdo, int $id): ?array
    {
        $sql = "SELECT * FROM pedidos WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        $pedido = $stmt->fetch();

        return $pedido ?: null;
    }

    // Listar pedidos de un usuario
    public static function listarPorUsuario(
        PDO $pdo,
        int $usuarioId
    ): array {
        $sql = "SELECT * FROM pedidos
                WHERE usuario_id = ?
                ORDER BY id DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuarioId]);

        return $stmt->fetchAll();
    }

    // Listar todos los pedidos
    public static function listarTodos(PDO $pdo): array
    {
        $sql = "SELECT * FROM pedidos ORDER BY id DESC";

        return $pdo->query($sql)->fetchAll();
    }

    // Actualizar el estado de un pedido
    public static function actualizarEstado(
        PDO $pdo,
        int $id,
        string $estado
    ): bool {
        $estadosPermitidos = [
            'pendiente',
            'confirmado',
            'enviado',
            'entregado',
            'cancelado'
        ];

        if (!in_array($estado, $estadosPermitidos, true)) {
            return false;
        }

        $sql = "UPDATE pedidos SET estado = ? WHERE id = ?";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([$estado, $id]);
    }
}
