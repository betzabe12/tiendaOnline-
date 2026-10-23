<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    public static function registro(PDO $pdo): void {
        $nombre = trim($_POST['nombre'] ?? '');
        $correo = strtolower(trim($_POST['correo'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($nombre === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            $_SESSION['flash'] = 'Por favor completa todos los datos y utiliza una contraseña de al menos 8 caracteres.';
            header('Location: ../index.php');
            exit;
        }
        if (Usuario::buscarPorCorreo($pdo, $correo)) {
            $_SESSION['flash'] = 'El correo electrónico ya se encuentra registrado.';
            header('Location: ../index.php');
            exit;
        }
        Usuario::crear($pdo, $nombre, $correo, password_hash($password, PASSWORD_DEFAULT));
        $_SESSION['flash'] = 'Registro exitoso. Ya puedes iniciar sesión con tu cuenta.';
        header('Location: ../index.php');
        exit;
    }

    public static function login(PDO $pdo): void {
        $correo = strtolower(trim($_POST['correo'] ?? ''));
        $password = $_POST['password'] ?? '';
        $usuario = Usuario::buscarPorCorreo($pdo, $correo);

        if (!$usuario || !password_verify($password, $usuario['password'])) {
            $_SESSION['flash'] = 'Correo electrónico o contraseña incorrectos.';
            header('Location: ../index.php');
            exit;
        }
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id'     => (int)$usuario['id'],
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
            'rol'    => $usuario['rol'] ?? 'cliente'
        ];
        header('Location: ../index.php');
        exit;
    }

    public static function salir(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: ../index.php');
        exit;
    }
}

// --- CÓDIGO DE ACTIVACIÓN DEL CONTROLADOR ---

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Requerimos la configuración de base de datos MySQL mediante la clase Database
require_once __DIR__ . '/../config/database.php';
$pdo = Database::conectar();

// Leemos la acción solicitada por URL (?action=login, register o logout)
$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'register') {
        AuthController::registro($pdo);
    } elseif ($action === 'login') {
        AuthController::login($pdo);
    }
} elseif ($action === 'logout') {
    AuthController::salir();
}
