<?php
/**
 * FashionCloud — Enrutador de Autenticación
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$pdo = Database::conectar();
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'register') {
        AuthController::registro($pdo);
    } elseif ($action === 'login') {
        AuthController::login($pdo);
    } else {
        $_SESSION['flash'] = 'Acción no válida.';
        header('Location: ../index.php');
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'logout') {
        AuthController::salir();
    } else {
        header('Location: ../index.php');
        exit;
    }
}