<?php

class Database
{
    private static ?PDO $pdo = null;

    public static function conectar(): PDO
    {
        if (self::$pdo === null) {
            try {
                // Definimos la ruta hacia el archivo SQLite dentro de la carpeta 'database'
                $rutaBaseDatos = __DIR__ . '/../database/tienda.sqlite';

                // Creamos la conexion PDO a SQLite
                self::$pdo = new PDO("sqlite:" . $rutaBaseDatos);
                
                // Configuramos PDO para que lance excepciones en caso de errores
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Configuramos para que retorne los datos como arreglos asociativos
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Creamos la tabla de usuarios automáticamente si no existe
                self::inicializarBaseDatos();

            } catch (PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }

    private static function inicializarBaseDatos(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                correo TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ";
        self::$pdo->exec($query);
    }
}
