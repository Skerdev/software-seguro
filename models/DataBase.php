<?php
class DataBase {
    # Conexión Local / Servidor
    public static function connection() {
        // Lee variables de entorno (definidas por phpunit.xml en entorno de pruebas);
        // si no existen, usa los valores por defecto de producción.
        $hostname = getenv('DB_HOST') ?: "localhost";
        $port     = getenv('DB_PORT') ?: "3306";
        $database = getenv('DB_NAME') ?: "db_inventory";
        $username = getenv('DB_USER') ?: "root";
        $password = getenv('DB_PASS') ?: "";

        try {
            $pdo = new PDO(
                "mysql:host=$hostname;port=$port;dbname=$database;charset=utf8",
                $username,
                $password
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            die("Error en la conexión a la base de datos.");
        }
    }
}
