
<?php

// ======================================================
// FASHIONCLOUD - CONTROLADOR DE AUTENTICACIÓN
// Registro, inicio y cierre de sesión
// ======================================================

// Iniciar sesión antes de utilizar $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar el modelo Usuario
$rutaUsuario = __DIR__ . '/../models/Usuario.php';

if (!file_exists($rutaUsuario)) {
    http_response_code(500);
    exit('Error: No se encontró el archivo Usuario.php.');
}

require_once $rutaUsuario;

// Cargar la configuración de base de datos
$rutaDatabase = __DIR__ . '/../config/database.php';

if (!file_exists($rutaDatabase)) {
    http_response_code(500);
    exit('Error: No se encontró el archivo database.php.');
}

require_once $rutaDatabase;


// ======================================================
// CLASE PRINCIPAL AUTHCONTROLLER
// ======================================================

class AuthController
{
    // --------------------------------------------------
    // REGISTRO DE USUARIO
    // --------------------------------------------------

    public static function registro(PDO $pdo): void
    {
        $nombre = trim($_POST['nombre'] ?? '');
        $correo = strtolower(trim($_POST['correo'] ?? ''));
        $password = $_POST['password'] ?? '';

        // Validar los datos recibidos
        if (
            $nombre === '' ||
            !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
            strlen($password) < 8
        ) {
            $_SESSION['flash'] =
                'Por favor completa todos los datos y utiliza una contraseña de al menos 8 caracteres.';

            header('Location: /');
            exit;
        }

        // Verificar si el correo ya existe
        $usuarioExistente = Usuario::buscarPorCorreo($pdo, $correo);

        if ($usuarioExistente) {
            $_SESSION['flash'] =
                'El correo electrónico ya se encuentra registrado.';

            header('Location: /');
            exit;
        }

        // Encriptar la contraseña
        $passwordEncriptado = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        // Guardar el nuevo usuario
        Usuario::crear(
            $pdo,
            $nombre,
            $correo,
            $passwordEncriptado
        );

        $_SESSION['flash'] =
            'Registro exitoso. Ya puedes iniciar sesión con tu cuenta.';

        header('Location: /');
        exit;
    }


    // --------------------------------------------------
    // INICIO DE SESIÓN
    // --------------------------------------------------

    public static function login(PDO $pdo): void
    {
        $correo = strtolower(trim($_POST['correo'] ?? ''));
        $password = $_POST['password'] ?? '';

        // Validar campos
        if (
            $correo === '' ||
            $password === '' ||
            !filter_var($correo, FILTER_VALIDATE_EMAIL)
        ) {
            $_SESSION['flash'] =
                'Debes ingresar un correo electrónico y una contraseña válidos.';

            header('Location: /');
            exit;
        }

        // Buscar usuario por correo
        $usuario = Usuario::buscarPorCorreo($pdo, $correo);

        // Verificar usuario y contraseña
        if (
            !$usuario ||
            !password_verify($password, $usuario['password'])
        ) {
            $_SESSION['flash'] =
                'Correo electrónico o contraseña incorrectos.';

            header('Location: /');
            exit;
        }

        // Regenerar identificador de sesión por seguridad
        session_regenerate_id(true);

        // Guardar los datos del usuario en la sesión
        $_SESSION['usuario'] = [
            'id' => (int) $usuario['id'],
            'nombre' => $usuario['nombre'],
            'correo' => $usuario['correo'],
            'rol' => $usuario['rol'] ?? 'cliente'
        ];

        $_SESSION['flash'] =
            'Bienvenido a FashionCloud, ' . $usuario['nombre'] . '.';

        // Regresar a la página principal
        header('Location: /');
        exit;
    }


    // --------------------------------------------------
    // CIERRE DE SESIÓN
    // --------------------------------------------------

    public static function salir(): void
    {
        // Vaciar los datos de sesión
        $_SESSION = [];

        // Eliminar la cookie de sesión
        if (ini_get('session.use_cookies')) {

            $parametros = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $parametros['path'],
                $parametros['domain'],
                $parametros['secure'],
                $parametros['httponly']
            );
        }

        // Destruir la sesión
        session_destroy();

        // Regresar a la página principal
        header('Location: /');
        exit;
    }
}


// ======================================================
// EJECUCIÓN DEL CONTROLADOR
// ======================================================

$accion = $_GET['action'] ?? '';

try {

    // Cerrar sesión
    if ($accion === 'logout' && $_SERVER['REQUEST_METHOD'] === 'GET') {

        AuthController::salir();

    }

    // Registrar o iniciar sesión
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $pdo = Database::conectar();

        if ($accion === 'register') {

            AuthController::registro($pdo);

        } elseif ($accion === 'login') {

            AuthController::login($pdo);

        } else {

            http_response_code(400);
            exit('Error: Acción no válida.');
        }

    } else {

        // Acción no reconocida
        header('Location: /');
        exit;
    }

} catch (PDOException $error) {

    http_response_code(500);

    exit(
        'Error en la base de datos: ' .
        htmlspecialchars($error->getMessage(), ENT_QUOTES, 'UTF-8')
    );
}
