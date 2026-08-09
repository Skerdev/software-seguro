<?php
ob_start();
session_start();

define('REDIRECT_LANDING', 'Location: ?c=Landing');

// REGISTRO DEL AUTOLOADER (Reemplaza los require_once de clases/modelos/controladores)
spl_autoload_register(function ($class) {
    $paths = ['models/', 'controllers/'];
    foreach ($paths as $path) {
        $file = __DIR__ . '/' . $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            break;
        }
    }
});

// 1. Obtener la petición del usuario
$controladorKey = $_REQUEST['c'] ?? $_REQUEST['do'] ?? 'Landing';

// 2. Instanciación explícita mediante switch
switch ($controladorKey) {
    case 'Login':
        $controlador = new Login();
        break;

    case 'Dashboard':
        $controlador = new Dashboard();
        break;

    case 'Users':
        $controlador = new Users();
        break;

    case 'Landing':
    default:
        $controlador = new Landing();
        $controladorKey = 'Landing'; // Normalización
        break;
}

// 3. Capturar acción
$accionInput = $_REQUEST['a'] ?? 'main';

// 4. Función de despacho explícito
function ejecutarAccion($controlador, string $accion): void {
    $metodosValidos = ['index', 'show', 'login', 'logout', 'create', 'edit', 'delete'];
    
    if (in_array($accion, $metodosValidos) && method_exists($controlador, $accion)) {
        $controlador->$accion();
    } else {
        $controlador->main();
    }
}

// 5. Lógica de renderizado
if ($controladorKey === 'Landing' || $controladorKey === 'Login') {
    require_once "views/company/header.view.php";
    ejecutarAccion($controlador, $accionInput);
    require_once "views/company/footer.view.php";
} elseif (!empty($_SESSION['session'])) {
    $session = $_SESSION['session'];
    $sessionSanitized = basename($session);

    $headerPath = "views/roles/" . $sessionSanitized . "/header.view.php";
    $footerPath = "views/roles/" . $sessionSanitized . "/footer.view.php";

    if (file_exists($headerPath) && file_exists($footerPath)) {
        require_once $headerPath;
        ejecutarAccion($controlador, $accionInput);
        require_once $footerPath;
    } else {
        header(REDIRECT_LANDING);
        exit;
    }
} else {
    header(REDIRECT_LANDING);
    exit;
}

ob_end_flush();
