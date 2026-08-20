<?php

ob_start();
session_start();

define('REDIRECT_LANDING', 'Location: ?c=Landing');

// REGISTRO DEL AUTOLOADER
// Reemplaza los require_once de clases/modelos/controladores
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
        $controladorKey = 'Landing';
        break;
}

// 3. Capturar acción
$accionInput = $_REQUEST['a'] ?? 'main';

// 4. Función de despacho explícito
function ejecutarAccion($controlador, string $accion): void
{
    /*
     * Acciones generales utilizadas por los controladores.
     */
    $metodosGenerales = [
        'index',
        'show',
        'login',
        'logout',
        'create',
        'edit',
        'delete'
    ];

    /*
     * Acciones específicas del controlador Users.
     */
    $metodosUsers = [
        'rolCreate',
        'rolRead',
        'rolUpdate',
        'rolDelete',
        'userCreate',
        'userRead',
        'userUpdate',
        'userDelete'
    ];

    /*
     * Se combinan las acciones permitidas.
     */
    $metodosValidos = array_merge(
        $metodosGenerales,
        $metodosUsers
    );

    /*
     * Solo se ejecuta una acción si:
     * 1. Está expresamente permitida.
     * 2. El método realmente existe en el controlador.
     */
    if (
        in_array($accion, $metodosValidos, true) &&
        method_exists($controlador, $accion)
    ) {
        $controlador->$accion();
        return;
    }

    /*
     * Si la acción no existe o no está permitida,
     * se ejecuta el método principal.
     */
    if (method_exists($controlador, 'main')) {
        $controlador->main();
        return;
    }

    /*
     * Último recurso: evitar que una petición inválida
     * provoque un error fatal si el controlador no tiene main().
     */
    header(REDIRECT_LANDING);
    exit;
}

// 5. Lógica de renderizado

// Landing y Login utilizan el layout público
if ($controladorKey === 'Landing' || $controladorKey === 'Login') {

    require_once "views/company/header.view.php";

    ejecutarAccion($controlador, $accionInput);

    require_once "views/company/footer.view.php";

} elseif (!empty($_SESSION['session'])) {

    $session = $_SESSION['session'];

    /*
     * Sanitización del nombre del rol antes de utilizarlo
     * para construir la ruta de las vistas.
     */
    $sessionSanitized = basename($session);

    $headerPath = "views/roles/" . $sessionSanitized . "/header.view.php";
    $footerPath = "views/roles/" . $sessionSanitized . "/footer.view.php";

    /*
     * Verificamos que las vistas correspondientes al rol existan.
     */
    if (file_exists($headerPath) && file_exists($footerPath)) {

        /*
         * Recuperar el perfil almacenado en sesión.
         *
         * Se mantiene allowed_classes para evitar que
         * unserialize() pueda instanciar clases arbitrarias.
         */
        $profile = isset($_SESSION['profile'])
            ? unserialize(
                $_SESSION['profile'],
                ['allowed_classes' => [User::class]]
            )
            : false;

        /*
         * Si el perfil no puede recuperarse correctamente,
         * destruimos la sesión y devolvemos al usuario al Landing.
         */
        if ($profile === false) {

            session_unset();
            session_destroy();

            header(REDIRECT_LANDING);
            exit;
        }

        require_once $headerPath;

        ejecutarAccion($controlador, $accionInput);

        require_once $footerPath;

    } else {

        /*
         * No existe el layout correspondiente al rol.
         */
        header(REDIRECT_LANDING);
        exit;
    }

} else {

    /*
     * Usuario no autenticado.
     */
    header(REDIRECT_LANDING);
    exit;
}

ob_end_flush();
