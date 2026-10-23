<?php
/**
 * FashionCloud — Configuración de Conexión a Base de Datos MySQL
 * Utiliza PDO para consultas seguras y preparadas.
 */

class Database
{
    private static ?PDO $pdo = null;

    /**
     * Retorna una instancia única de PDO (patrón Singleton)
     */
    public static function conectar(): PDO
    {
        if (self::$pdo === null) {
            // Parámetros de conexión compatibles con XAMPP y variables de entorno para Render/Nube
            $host = getenv('DB_HOST') ?: '127.0.0.1';
            $port = getenv('DB_PORT') ?: '3306';
            $dbname = getenv('DB_NAME') ?: 'fashioncloud';
            $username = getenv('DB_USER') ?: 'root';
            $password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                http_response_code(500);
                // Mensaje claro y amigable en caso de que MySQL (XAMPP) no esté iniciado
                exit('No se pudo conectar a MySQL. Asegúrate de iniciar el módulo MySQL en el panel de XAMPP y de haber importado la base de datos "fashioncloud". Detalle técnico: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
