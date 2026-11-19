<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

if (empty($_SESSION['usuario'])) {
    fc_redirect('frontend/auth.php?modo=login');
}

if (($_SESSION['usuario']['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

$pdo = Database::conectar();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !fc_verify_csrf()) {
    http_response_code(403);
    exit('Recarga el formulario e inténtalo nuevamente.');
}

function admin_h($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function admin_inicio(string $titulo): void
{
    $mensaje = $_SESSION['flash'] ?? '';
    unset($_SESSION['flash'], $_SESSION['flash_type']);
    ?>
    <!doctype html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= admin_h($titulo) ?> | FashionCloud</title>
        <link rel="stylesheet" href="<?= admin_h(fc_url('frontend/css/compras.css')) ?>">
    </head>
    <body>
    <main>
        <a href="Panel_admin.php">← Volver al panel</a>
        <h1><?= admin_h($titulo) ?></h1>
        <?php if ($mensaje !== ''): ?><p role="status"><?= admin_h($mensaje) ?></p><?php endif; ?>
    <?php
}

function admin_token(): void
{
    ?>
    <input type="hidden" name="csrf_token" value="<?= admin_h(fc_csrf_token()) ?>">
    <?php
}
