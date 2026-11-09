<?php
/**
 * FashionCloud — Enrutador de Categorías
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/CategoriaController.php';

$pdo = Database::conectar();
$action = $_GET['action'] ?? 'listar';

// Ejemplo para retornar datos o manejar peticiones de categoría
$categorias = CategoriaController::listar($pdo);

// Si se requiere en formato JSON para APIs o AJAX:
if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($categorias);
    exit;
}