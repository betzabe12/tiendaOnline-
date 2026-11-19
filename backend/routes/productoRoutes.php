<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/ProductoController.php';

$accion = $_GET['action'] ?? '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET' && $accion === '') {
    $productos = Producto::listar(Database::conectar());
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($productos, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: GET, POST');
    http_response_code(405);
    exit('Método no permitido.');
}
if (($_SESSION['usuario']['rol'] ?? '') !== 'admin' || !fc_verify_csrf()) {
    http_response_code(403);
    exit('Acceso denegado o formulario vencido.');
}

switch ($accion) {
    case 'crear':
        ProductoController::crear(Database::conectar());
        break;
    case 'eliminar':
        ProductoController::eliminar(Database::conectar());
        break;
    default:
        http_response_code(400);
        exit('Acción no válida.');
}
