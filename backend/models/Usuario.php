<?php

class Usuario
{
    public static function crear(PDO $pdo, string $nombre, string $correo, string $password): bool
    {
        $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, correo, password) VALUES (?, ?, ?)');
        return $stmt->execute([$nombre, $correo, $password]);
    }

    /** Incluye el hash únicamente porque el login necesita verificarlo. Nunca mostrar este resultado directamente. */
    public static function buscarPorCorreo(PDO $pdo, string $correo): ?array
    {
        $stmt = $pdo->prepare('SELECT id, nombre, correo, password, rol FROM usuarios WHERE correo = ? LIMIT 1');
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    /** Datos públicos/administrativos sin el hash de la contraseña. */
    public static function listar(PDO $pdo): array
    {
        return $pdo->query('SELECT id, nombre, correo, rol, created_at FROM usuarios ORDER BY id DESC')->fetchAll();
    }

    public static function buscarPorId(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare('SELECT id, nombre, correo, rol, created_at FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();
        return $usuario ?: null;
    }

    public static function actualizar(PDO $pdo, int $id, string $nombre, string $correo): bool
    {
        $stmt = $pdo->prepare('UPDATE usuarios SET nombre = ?, correo = ? WHERE id = ?');
        return $stmt->execute([$nombre, $correo, $id]);
    }

    public static function eliminar(PDO $pdo, int $id): bool
    {
        $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
