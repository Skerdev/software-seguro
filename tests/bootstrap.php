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
        $pdo->exec("INSERT INTO ROLES VALUES ('R001', 'admin')");
        $pdo->exec("INSERT INTO ROLES VALUES ('R002', 'seller')");
        $pdo->exec("INSERT INTO ROLES VALUES ('R003', 'customer')");
        
        $pdo->exec("INSERT INTO USERS VALUES ('R001', 'U001', 'Admin', 'Sistema', '12345', 'admin@test.com', '" . sha1('admin123') . "', 1)");
        $pdo->exec("INSERT INTO USERS VALUES ('R002', 'U002', 'Vendedor', 'Prueba', '67890', 'seller@test.com', '" . sha1('seller123') . "', 1)");
        $pdo->exec("INSERT INTO USERS VALUES ('R003', 'U003', 'Cliente', 'Test', '11111', 'customer@test.com', '" . sha1('customer123') . "', 1)");
        return true;
    } catch (Exception $e) {
        echo "Error al resetear la base de datos: " . $e->getMessage();
        return false;
    }
}