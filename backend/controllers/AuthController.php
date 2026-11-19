
<?php
/**
 * Controlador de autenticación FashionCloud.
 * El despacho de rutas está en routes/authRoutes.php.
 */

require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    private static function validarCsrf(): void
    {
        if (!function_exists('fc_verify_csrf') || !fc_verify_csrf()) {
            http_response_code(419);
            exit('La sesión del formulario expiró. Vuelve a intentarlo.');
        }
    }

    /**
     * Registro de nuevos usuarios.
     */
    public static function registro(PDO $pdo): void
    {
        self::validarCsrf();

        $nombre = trim((string)($_POST['nombre'] ?? ''));
        $correo = strtolower(trim((string)($_POST['correo'] ?? '')));
        $password = (string)($_POST['password'] ?? '');

        if (
            $nombre === ''
            || mb_strlen($nombre, 'UTF-8') > 120
            || !filter_var($correo, FILTER_VALIDATE_EMAIL)
            || strlen($correo) > 150
            || strlen($password) < 8
            || strlen($password) > 4096
        ) {
            fc_set_flash(
                'Revisa tus datos. El nombre debe tener hasta 120 caracteres y la contraseña al menos 8.',
                'error'
            );

            fc_redirect('frontend/auth.php?modo=register');
        }

        if (Usuario::buscarPorCorreo($pdo, $correo)) {
            fc_set_flash(
                'Ese correo electrónico ya está registrado.',
                'error'
            );

            fc_redirect('frontend/auth.php?modo=register');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        if (!$hash) {
            throw new RuntimeException(
                'No fue posible proteger la contraseña.'
            );
        }

        try {
            Usuario::crear($pdo, $nombre, $correo, $hash);

        } catch (PDOException $e) {

            if ($e->getCode() === '23000') {
                fc_set_flash(
                    'Ese correo electrónico ya está registrado.',
                    'error'
                );

                fc_redirect('frontend/auth.php?modo=register');
            }

            throw $e;
        }

        fc_set_flash(
            'Tu cuenta fue creada correctamente. Ahora puedes iniciar sesión.',
            'success'
        );

        fc_redirect('frontend/auth.php?modo=login');
    }

    /**
     * Inicio de sesión.
     */
    public static function login(PDO $pdo): void
    {
        self::validarCsrf();

        $correo = strtolower(trim((string)($_POST['correo'] ?? '')));
        $password = (string)($_POST['password'] ?? '');

        if (
            !filter_var($correo, FILTER_VALIDATE_EMAIL)
            || $password === ''
        ) {
            fc_set_flash(
                'Ingresa un correo electrónico y una contraseña válidos.',
                'error'
            );

            fc_redirect('frontend/auth.php?modo=login');
        }

        $usuario = Usuario::buscarPorCorreo($pdo, $correo);

        if (
            !$usuario
            || !password_verify($password, $usuario['password'])
        ) {
            fc_set_flash(
                'Correo electrónico o contraseña incorrectos.',
                'error'
            );

            fc_redirect('frontend/auth.php?modo=login');
        }

        // Renovar el identificador de sesión.
        session_regenerate_id(true);

        $_SESSION['usuario'] = [
            'id' => (int)$usuario['id'],
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
            'rol' => $usuario['rol'] ?? 'cliente',
        ];

        // Renovar el token CSRF.
        unset($_SESSION['csrf_token']);

        fc_set_flash(
            'Bienvenido/a a FashionCloud, ' . $usuario['nombre'] . '.',
            'success'
        );

        // Redirección según el rol.
        if ($_SESSION['usuario']['rol'] === 'admin') {
            fc_redirect('backend/admin/Panel_admin.php');
        }

        fc_redirect('frontend/index.php');
    }

    /**
     * Cierre de sesión.
     */
    public static function salir(): void
    {
        self::validarCsrf();

        // Vaciar los datos de sesión.
        $_SESSION = [];

        // Eliminar la cookie de sesión.
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(session_name(), '', [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        // Destruir la sesión.
        session_destroy();

        // Regresar a la tienda.
        header('Location: ' . fc_url('frontend/index.php'));
        exit;
    }
}
