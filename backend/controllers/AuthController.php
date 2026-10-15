<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    public static function registro(PDO $pdo): void {
        $nombre = trim($_POST['nombre'] ?? '');
        $correo = strtolower(trim($_POST['correo'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($nombre === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            $_SESSION['flash'] = 'Completa los datos y utiliza una contraseña de al menos 8 caracteres.';
            header('Location: ../views/registro.php');
            exit;
        }
        if (Usuario::buscarPorCorreo($pdo, $correo)) {
            $_SESSION['flash'] = 'Ese correo ya está registrado.';
            header('Location: ../views/registro.php');
            exit;
        }
        Usuario::crear($pdo, $nombre, $correo, password_hash($password, PASSWORD_DEFAULT));
        $_SESSION['flash'] = 'Registro exitoso. Ya puedes iniciar sesión.';
        header('Location: ../views/login.php');
        exit;
    }

    public static function login(PDO $pdo): void {
        $correo = strtolower(trim($_POST['correo'] ?? ''));
        $password = $_POST['password'] ?? '';
        $usuario = Usuario::buscarPorCorreo($pdo, $correo);

        if (!$usuario || !password_verify($password, $usuario['password'])) {
            $_SESSION['flash'] = 'Correo o contraseña incorrectos.';
            header('Location: ../views/login.php');
            exit;
        }
        session_regenerate_id(true);
        $_SESSION['usuario'] = [
            'id' => (int)$usuario['id'],
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
            'rol' => $usuario['rol']
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

