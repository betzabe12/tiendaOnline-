<?php
/** Punto único de entrada para registro, login y logout. */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$action = (string)($_GET['action'] ?? '');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    if ($method !== 'POST') {
        http_response_code(405);
        header('Allow: POST');
        exit('Método no permitido.');
    }

    if ($action === 'register' || $action === 'login') {
        $pdo = Database::conectar();
        if ($action === 'register') {
            AuthController::registro($pdo);
        }
        AuthController::login($pdo);
    }

    if ($action === 'logout') {
        AuthController::salir();
    }

    http_response_code(400);
    exit('Acción no válida.');
} catch (PDOException $e) {
    error_log('FashionCloud Auth PDO: ' . $e->getMessage());
    fc_set_flash('No se pudo completar la operación. Verifica la base de datos e inténtalo nuevamente.', 'error');
    fc_redirect('frontend/auth.php?modo=' . ($action === 'register' ? 'register' : 'login'));
} catch (Throwable $e) {
    error_log('FashionCloud Auth: ' . $e->getMessage());
    http_response_code(500);
    exit('Ocurrió un error interno. Inténtalo nuevamente.');
}
