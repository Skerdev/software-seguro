php
ob_start();
session_start();

require_once "models/DataBase.php";

// 1. Obtener la petición del usuario
$controladorInput = $_REQUEST['c'] ?? $_REQUEST['do'] ?? 'Landing';

// 2. Definir una lista blanca (Allowlist) de controladores permitidos y sus archivos
$controladoresPermitidos = [
    'Landing'   => 'controllers/Landing.php',
    'Login'     => 'controllers/Login.php',
    'Dashboard' => 'controllers/Dashboard.php',
    'Users'     => 'controllers/Users.php',
    // Agrega aquí los demás controladores válidos del proyecto
];

// 3. Validar si el controlador solicitado está en la lista blanca
if (!array_key_exists($controladorInput, $controladoresPermitidos)) {
    // Si no está permitido o no existe, redirigir al controlador por defecto
    header("Location: ?c=Landing");
    exit;
}

// 4. Cargar el archivo de manera segura desde la lista blanca (sin datos directos de $_REQUEST)
$route_controller = $controladoresPermitidos[$controladorInput];
require_once $route_controller;

// 5. Instanciar el controlador seguro
$controlador = new $controladorInput();

// 6. Validar la acción enviada por el usuario
$accion = $_REQUEST['a'] ?? 'main';

// Opcional: Validar que el método exista en la clase para evitar Fatal Errors
if (!method_exists($controlador, $accion)) {
    $accion = 'main';
}

// 7. Lógica de renderizado según vistas y sesión
$vista = $controladorInput;

if ($vista === 'Landing' || $vista === 'Login') {
    require_once "views/company/header.view.php";
    call_user_func([$controlador, $accion]);
    require_once "views/company/footer.view.php";
} elseif (!empty($_SESSION['session'])) {
    require_once "models/User.php";
    $perfil = unserialize($_SESSION['profile']);
    $session = $_SESSION['session'];

    // Sanitización básica del rol para evitar saltos en directorios de vistas
    $sessionSanitized = basename($session);

    $headerPath = "views/roles/" . $sessionSanitized . "/header.view.php";
    $footerPath = "views/roles/" . $sessionSanitized . "/footer.view.php";

    if (file_exists($headerPath) && file_exists($footerPath)) {
        require_once $headerPath;
        call_user_func([$controlador, $accion]);
        require_once $footerPath;
    } else {
        header("Location: ?c=Landing");
        exit;
    }
} else {
    header("Location: ?c=Landing");
    exit;
}

ob_end_flush();
