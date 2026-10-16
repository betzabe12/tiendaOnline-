<?php

class Usuario
{
    public static function crear(PDO $pdo, string $nombre, string $correo, string $password): bool
    {
        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nombre, correo, password)
            VALUES (?, ?, ?)
        ");

        return $stmt->execute([
            $nombre,
            $correo,
            $password
        ]);
    }

    public static function buscarPorCorreo(PDO $pdo, string $correo): ?array
    {
        $stmt = $pdo->prepare("
            SELECT *
            FROM usuarios
            WHERE correo = ?
            LIMIT 1
        ");

        $stmt->execute([$correo]);

        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    public static function listar(PDO $pdo): array
    {
        $stmt = $pdo->query("
            SELECT *
            FROM usuarios
            ORDER BY id DESC
        ");

        return $stmt->fetchAll();
    }

    public static function buscarPorId(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare("
            SELECT *
            FROM usuarios
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        $usuario = $stmt->fetch();

        return $usuario ?: null;
    }

    public static function actualizar(
        PDO $pdo,
        int $id,
        string $nombre,
        string $correo
    ): bool {
        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET nombre = ?, correo = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $nombre,
            $correo,
            $id
        ]);
    }

    public static function eliminar(PDO $pdo, int $id): bool
    {
        $stmt = $pdo->prepare("
            DELETE FROM usuarios
            WHERE id = ?
        ");

        return $stmt->execute([$id]);
    }
}