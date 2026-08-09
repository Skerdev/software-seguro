<?php
ob_start();
session_start();

define('REDIRECT_LANDING', 'Location: ?c=Landing');

require_once "models/DataBase.php";

// 1. Obtener la petición del usuario
$controladorKey = $_REQUEST['c'] ?? $_REQUEST['do'] ?? 'Landing';

// 2. Instanciación explícita mediante switch (elimina 'new $variable' para SonarQube)
switch ($controladorKey) {
    case 'Login':
        require_once 'controllers/Login.php';
        $controlador = new Login();
        break;

    case 'Dashboard':
        require_once 'controllers/Dashboard.php';
        $controlador = new Dashboard();
        break;

    case 'Users':
        require_once 'controllers/Users.php';
        $controlador = new Users();
        break;

    case 'Landing':
    default:
        require_once 'controllers/Landing.php';
        $controlador = new Landing();
        $controladorKey = 'Landing'; // Normalización
        break;
}

// 3. Capturar acción
$accionInput = $_REQUEST['a'] ?? 'main';

// 4. Función de despacho explícito para evitar la sintaxis '$controlador->$accion()'
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
    require_once "models/User.php";

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
