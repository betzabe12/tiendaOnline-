<?php

class Categoria
{
    // Listar categorías activas
    public static function listar(PDO $pdo): array
    {
        $sql = "SELECT * FROM categorias
                WHERE estado = 1
                ORDER BY nombre ASC";

        return $pdo->query($sql)->fetchAll();
    }

    // Buscar una categoría por su ID
    public static function buscarPorId(PDO $pdo, int $id): ?array
    {
        $sql = "SELECT * FROM categorias
                WHERE id = ? AND estado = 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);

        $categoria = $stmt->fetch();

        return $categoria ?: null;
    }

    // Registrar una categoría
    public static function crear(
        PDO $pdo,
        string $nombre,
        ?string $descripcion = null
    ): bool {
        $sql = "INSERT INTO categorias (nombre, descripcion)
                VALUES (?, ?)";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([$nombre, $descripcion]);
    }

    // Actualizar una categoría
    public static function actualizar(
        PDO $pdo,
        int $id,
        string $nombre,
        ?string $descripcion = null
    ): bool {
        $sql = "UPDATE categorias
                SET nombre = ?, descripcion = ?
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);

        return $stmt->execute([$nombre, $descripcion, $id]);
    }

    // Desactivar una categoría
    public static function eliminar(PDO $pdo, int $id): bool
    {
        $sql = "UPDATE categorias
                SET estado = 0
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
