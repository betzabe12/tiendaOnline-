<?php
class CategoriaController {
    public static function listar(PDO $pdo): array {
        return $pdo->query('SELECT * FROM categorias WHERE estado = 1 ORDER BY nombre')->fetchAll();
    }
}
