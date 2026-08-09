<?php
ob_start();
session_start();

define('REDIRECT_LANDING', 'Location: ?c=Landing');

require_once "models/DataBase.php";

// 1. Obtener la clave solicitada
$controladorKey = $_REQUEST['c'] ?? $_REQUEST['do'] ?? 'Landing';

// 2. Lista blanca con ruta del archivo, nombre exacto de la clase y métodos permitidos
$controladoresPermitidos = [
    'Landing' => [
        'file'    => 'controllers/Landing.php',
        'class'   => 'Landing',
        'actions' => ['main', 'index', 'show']
    ],
    'Login' => [
        'file'    => 'controllers/Login.php',
        'class'   => 'Login',
        'actions' => ['main', 'login', 'logout']
    ],
    'Dashboard' => [
        'file'    => 'controllers/Dashboard.php',
        'class'   => 'Dashboard',
        'actions' => ['main', 'index']
    ],
    'Users' => [
        'file'    => 'controllers/Users.php',
        'class'   => 'Users',
        'actions' => ['main', 'index', 'create', 'edit', 'delete']
    ]
];

// 3. Validar si el controlador solicitado está en la lista blanca
if (!array_key_exists($controladorKey, $controladoresPermitidos)) {
    header(REDIRECT_LANDING);
    exit;
}

$config = $controladoresPermitidos[$controladorKey];

// 4. Cargar archivo e instanciar usando la definición estática de la lista blanca
require_once $config['file'];
$className = $config['class'];
$controlador = new $className();

// 5. Validar y resolver la acción contra la lista de métodos permitidos
$accionInput = $_REQUEST['a'] ?? 'main';
$accion = in_array($accionInput, $config['actions'], true) && method_exists($controlador, $accionInput)
    ? $accionInput
    : 'main';

// 6. Lógica de renderizado
if ($controladorKey === 'Landing' || $controladorKey === 'Login') {
    require_once "views/company/header.view.php";
    $controlador->$accion();
    require_once "views/company/footer.view.php";
} elseif (!empty($_SESSION['session'])) {
    require_once "models/User.php";
    
    $session = $_SESSION['session'];
    $sessionSanitized = basename($session);

    $headerPath = "views/roles/" . $sessionSanitized . "/header.view.php";
    $footerPath = "views/roles/" . $sessionSanitized . "/footer.view.php";

    if (file_exists($headerPath) && file_exists($footerPath)) {
        require_once $headerPath;
        $controlador->$accion();
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
