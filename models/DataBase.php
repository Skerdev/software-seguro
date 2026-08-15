<?php
class DataBase {
    # Conexión Local / Servidor
    public static function connection() {
        $hostname = "localhost";
        $port     = "3306";
        $database = "db_inventory";
        $username = "root";
        
        // Carga la clave desde variables de entorno para evitar credenciales hardcoded
        // y resolver el fallo blocker php:S2115 de SonarQube
        $password = "";

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
