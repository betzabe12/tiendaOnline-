<?php

class Producto
{
    // Listar todos los productos activos
    public static function listar(PDO $pdo): array
    {
        $sql = "SELECT * FROM productos
                WHERE estado = 1
                ORDER BY nombre ASC";

        return $pdo->query($sql)->fetchAll();
    }

    // Buscar un producto por su ID
    public static function buscarPorId(PDO $pdo, int $id): ?array
    {
        $sql = "SELECT * FROM productos
                WHERE id = ? AND estado = 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        $producto = $stmt->fetch();

        return $producto ?: null;
    }

    // Buscar productos por nombre
    public static function buscar(PDO $pdo, string $nombre): array
    {
        $sql = "SELECT * FROM productos
                WHERE estado = 1 AND nombre LIKE ?
                ORDER BY nombre ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['%' . $nombre . '%']);

        return $stmt->fetchAll();
    }

    // Crear un producto
    public static function crear(PDO $pdo, array $datos): bool
    {
        $sql = "INSERT INTO productos
                (codigo, nombre, descripcion, precio, stock,
                 talla, color, imagen_url, categoria_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([
            $datos['codigo'],
            $datos['nombre'],
            $datos['descripcion'] ?? null,
            $datos['precio'],
            $datos['stock'],
            $datos['talla'] ?? null,
            $datos['color'] ?? null,
            $datos['imagen_url'] ?? null,
            $datos['categoria_id'] ?? null
        ]);
    }

    // Actualizar el stock de un producto
    public static function actualizarStock(
        PDO $pdo,
        int $id,
        int $stock
    ): bool {
        $sql = "UPDATE productos
                SET stock = ?
                WHERE id = ? AND estado = 1";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([$stock, $id]);
    }

    // Desactivar un producto sin eliminarlo de la base de datos
    public static function eliminar(PDO $pdo, int $id): bool
    {
        $sql = "UPDATE productos
                SET estado = 0
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([$id]);
    }
}
