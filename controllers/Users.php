<?php

class Users
{
    private $session;

    private const ROLE_ADMIN = 'admin';
    private const ROLE_SELLER = 'seller';

    private const REDIRECT_DASHBOARD = 'Location: ?c=Dashboard';
    private const REDIRECT_ROLE_READ = 'Location: ?c=Users&a=rolRead';
    private const REDIRECT_USER_READ = 'Location: ?c=Users&a=userRead';

    public function __construct()
    {
        $this->session = $_SESSION['session'] ?? null;
    }

    /**
     * Verifica si el usuario tiene uno de los roles permitidos.
     */
    private function hasRole(array $roles): bool
    {
        return in_array($this->session, $roles, true);
    }

    /**
     * Redirige al dashboard y finaliza la ejecución.
     */
    private function redirectDashboard(): void
    {
        header(self::REDIRECT_DASHBOARD);
        exit;
    }

    /**
     * Controlador principal.
     */
    public function main(): void
    {
        $this->redirectDashboard();
    }

    /**
     * Crear Rol
     * Solo administrador.
     */
    public function rolCreate(): void
    {
        if (!$this->hasRole([self::ROLE_ADMIN])) {
            $this->redirectDashboard();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            require_once 'views/modules/users/rol_create.view.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $rolName = trim($_POST['rol_name'] ?? '');

            if ($rolName === '') {
                $message = 'El nombre del rol es obligatorio.';
                require_once 'views/modules/users/rol_create.view.php';
                return;
            }

            $rol = new Rol([
                'rolName' => $rolName
            ]);

            $rol->create();

            header(self::REDIRECT_ROLE_READ);
            exit;
        }

        $this->redirectDashboard();
    }

    /**
     * Consultar Roles
     * Solo administrador.
     */
    public function rolRead(): void
    {
        if (!$this->hasRole([self::ROLE_ADMIN])) {
            $this->redirectDashboard();
        }

        $rolModel = new Rol();
        $roles = $rolModel->readAll();

        require_once 'views/modules/users/rol_read.view.php';
    }

    /**
     * Actualizar Rol
     * Solo administrador.
     */
    public function rolUpdate(): void
    {
        if (!$this->hasRole([self::ROLE_ADMIN])) {
            $this->redirectDashboard();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $rolCode = $_GET['idRol'] ?? '';

            if ($rolCode === '') {
                header(self::REDIRECT_ROLE_READ);
                exit;
            }

            $rolModel = new Rol();
            $rolId = $rolModel->getByCode($rolCode);

            if (!$rolId) {
                header(self::REDIRECT_ROLE_READ);
                exit;
            }

            require_once 'views/modules/users/rol_update.view.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $rolCode = trim($_POST['rol_code'] ?? '');
            $rolName = trim($_POST['rol_name'] ?? '');

            if ($rolCode === '' || $rolName === '') {
                header(self::REDIRECT_ROLE_READ);
                exit;
            }

            $rolUpdate = new Rol([
                'rolCode' => $rolCode,
                'rolName' => $rolName
            ]);

            $rolUpdate->update();

            header(self::REDIRECT_ROLE_READ);
            exit;
        }

        $this->redirectDashboard();
    }

    /**
     * Eliminar Rol
     * Solo administrador.
     */
    public function rolDelete(): void
    {
        if (!$this->hasRole([self::ROLE_ADMIN])) {
            $this->redirectDashboard();
        }

        $rolCode = $_GET['idRol'] ?? '';

        if ($rolCode !== '') {
            $rol = new Rol();
            $rol->delete($rolCode);
        }

        header(self::REDIRECT_ROLE_READ);
        exit;
    }

    /**
     * Crear Usuario
     * Administrador y vendedor.
     */
    public function userCreate(): void
    {
        if (!$this->hasRole([self::ROLE_ADMIN, self::ROLE_SELLER])) {
            $this->redirectDashboard();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $rolModel = new Rol();
            $roles = $rolModel->readAll();

            require_once 'views/modules/users/user_create.view.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $password = $_POST['user_pass'] ?? '';
            $passwordConfirmation = $_POST['user_pass_conf'] ?? '';

            if ($password === '' || $password !== $passwordConfirmation) {
                $rolModel = new Rol();
                $roles = $rolModel->readAll();

                $message = 'Las contraseñas no coinciden o están vacías.';

                require_once 'views/modules/users/user_create.view.php';
                return;
            }

            $user = new User([
                'rolCode' => $_POST['rol_code'] ?? '',
                'userName' => trim($_POST['user_name'] ?? ''),
                'userLastname' => trim($_POST['user_lastname'] ?? ''),
                'userId' => trim($_POST['user_id'] ?? ''),
                'userEmail' => trim($_POST['user_email'] ?? ''),
                'userPass' => $password,
                'userState' => $_POST['user_state'] ?? ''
            ]);

            $user->create();

            header(self::REDIRECT_USER_READ);
            exit;
        }

        $this->redirectDashboard();
    }

    /**
     * Consultar Usuarios
     * Administrador y vendedor.
     */
    public function userRead(): void
    {
        if (!$this->hasRole([self::ROLE_ADMIN, self::ROLE_SELLER])) {
            $this->redirectDashboard();
        }

        $userModel = new User();
        $users = $userModel->readAll();

        require_once 'views/modules/users/user_read.view.php';
    }

    /**
     * Actualizar Usuario
     * Administrador y vendedor.
     */
    public function userUpdate(): void
    {
        if (!$this->hasRole([self::ROLE_ADMIN, self::ROLE_SELLER])) {
            $this->redirectDashboard();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $userCode = $_GET['idUser'] ?? '';

            if ($userCode === '') {
                header(self::REDIRECT_USER_READ);
                exit;
            }

            $rolModel = new Rol();
            $roles = $rolModel->readAll();

            $userModel = new User();
            $user = $userModel->getByCode($userCode);

            if (!$user) {
                header(self::REDIRECT_USER_READ);
                exit;
            }

            $state = [
                0 => 'Inactivo',
                1 => 'Activo'
            ];

            require_once 'views/modules/users/user_update.view.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $userCode = trim($_POST['user_code'] ?? '');

            if ($userCode === '') {
                header(self::REDIRECT_USER_READ);
                exit;
            }

            $userModel = new User();

            /*
             * Si el campo de contraseña viene vacío,
             * conservamos la contraseña actual.
             */
            $currentUser = $userModel->getByCode($userCode);

            if (!$currentUser) {
                header(self::REDIRECT_USER_READ);
                exit;
            }

            $password = $_POST['user_pass'] ?? '';

            if ($password === '') {
                $password = $currentUser->getUserPassword();
            }

            $userUpdate = new User([
                'rolCode' => $_POST['rol_code'] ?? '',
                'userCode' => $userCode,
                'userName' => trim($_POST['user_name'] ?? ''),
                'userLastname' => trim($_POST['user_lastname'] ?? ''),
                'userId' => trim($_POST['user_id'] ?? ''),
                'userEmail' => trim($_POST['user_email'] ?? ''),
                'userPass' => $password,
                'userState' => $_POST['user_state'] ?? ''
            ]);

            $userUpdate->update();

            header(self::REDIRECT_USER_READ);
            exit;
        }

        $this->redirectDashboard();
    }

    /**
     * Eliminar Usuario
     * Solo administrador.
     */
    public function userDelete(): void
    {
        if (!$this->hasRole([self::ROLE_ADMIN])) {
            $this->redirectDashboard();
        }

        $userCode = $_GET['idUser'] ?? '';

        if ($userCode !== '') {
            $user = new User();
            $user->delete($userCode);
        }

        header(self::REDIRECT_USER_READ);
        exit;
    }
}
