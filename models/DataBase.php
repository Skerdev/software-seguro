<?php
class DataBase {
    private static $connection = null;

    # Conexión Local / Servidor (reutiliza la misma conexión durante todo el
    # ciclo de vida del proceso, para que las transacciones abiertas en un
    # punto del código sean visibles desde cualquier otro objeto que también
    # llame a DataBase::connection())
    public static function connection() {
        if (self::$connection !== null) {
            return self::$connection;
        }

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
            self::$connection = $pdo;
            return $pdo;
        } catch (PDOException $e) {
            error_log("Error de conexión: " . $e->getMessage());
            die("Error en la conexión a la base de datos.");
        }
    }
}

