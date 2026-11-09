
<?php
/**
 * FashionCloud - Conexion MySQL
 * Compatible con XAMPP y Render.
 */

class Database
{
    private static ?PDO $pdo = null;

    public static function conectar(): PDO
    {
        if (self::$pdo === null) {

            // Variables de entorno para Render.
            // Valores predeterminados para XAMPP.
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $port = getenv('DB_PORT') ?: '3306';
            $dbname = getenv('DB_NAME') ?: 'fashioncloud';
            $username = getenv('DB_USER') ?: 'root';

            $password = getenv('DB_PASSWORD');

            // Compatibilidad con el nombre anterior.
            if ($password === false) {
                $password = getenv('DB_PASS');
            }

            if ($password === false) {
                $password = '';
            }

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$pdo = new PDO(
                    $dsn,
                    $username,
                    $password,
                    $options
                );

            } catch (PDOException $e) {
                error_log('Error de conexion FashionCloud: ' . $e->getMessage());

                http_response_code(500);
                exit('No se pudo conectar con la base de datos. Verifica la configuracion de MySQL.');
            }
        }

        return self::$pdo;
    }
}
