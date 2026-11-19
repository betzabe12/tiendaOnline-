<?php
/** Configuración común de FashionCloud para XAMPP. */

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/** Devuelve la ruta base del proyecto aunque esté dentro de una carpeta de htdocs. */
function fc_base_path(): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if (preg_match('~^(.*?)/(?:backend|frontend)(?:/|$)~', $script, $matches)) {
        return rtrim($matches[1], '/');
    }
    return '';
}

function fc_url(string $path = ''): string
{
    return fc_base_path() . '/' . ltrim($path, '/');
}

function fc_redirect(string $path): never
{
    header('Location: ' . fc_url($path));
    exit;
}

function fc_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function fc_verify_csrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function fc_set_flash(string $message, string $type = 'info'): void
{
    $_SESSION['flash'] = $message;
    $_SESSION['flash_type'] = $type;
}
