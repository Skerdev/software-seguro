<?php
// tests/bootstrap.php
// Cargar el autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Configurar variables de entorno para pruebas
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_inventory_test');
define('DB_USER', 'root');
define('DB_PASS', '');

// Cargar las clases principales del proyecto
require_once __DIR__ . '/../models/DataBase.php';
require_once __DIR__ . '/../models/User.php';

// Iniciar sesión para las pruebas (simular)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Limpiar sesión antes de cada prueba
$_SESSION = [];

// Función de ayuda para resetear la base de datos de pruebas
function resetTestDatabase() {
    try {
        $pdo = DataBase::connection();
        // Eliminar datos existentes en orden correcto (primero users, luego roles)
        $pdo->exec("DELETE FROM USERS");
        $pdo->exec("DELETE FROM ROLES");
        
        // Insertar datos de prueba iniciales
        // Nota: rol_code y user_code son INT AUTO_INCREMENT en el esquema real,
        // por eso se usan enteros explícitos (1, 2, 3) y no códigos tipo 'R001'.
        $pdo->exec("INSERT INTO ROLES (rol_code, rol_name) VALUES (1, 'admin')");
        $pdo->exec("INSERT INTO ROLES (rol_code, rol_name) VALUES (2, 'seller')");
        $pdo->exec("INSERT INTO ROLES (rol_code, rol_name) VALUES (3, 'customer')");

        $pdo->exec("INSERT INTO USERS (rol_code, user_code, user_name, user_lastname, user_id, user_email, user_pass, user_state) VALUES (1, 1, 'Admin', 'Sistema', '12345', 'admin@test.com', '" . hash('sha256', 'admin123') . "', 1)");
        $pdo->exec("INSERT INTO USERS (rol_code, user_code, user_name, user_lastname, user_id, user_email, user_pass, user_state) VALUES (2, 2, 'Vendedor', 'Prueba', '67890', 'seller@test.com', '" . hash('sha256', 'seller123') . "', 1)");
        $pdo->exec("INSERT INTO USERS (rol_code, user_code, user_name, user_lastname, user_id, user_email, user_pass, user_state) VALUES (3, 3, 'Cliente', 'Test', '11111', 'customer@test.com', '" . hash('sha256', 'customer123') . "', 1)");
        return true;
    } catch (Exception $e) {
        echo "Error al resetear la base de datos: " . $e->getMessage();
        return false;
    }
}

