<?php
require_once "models/User.php";

class Users {
    private $session;

    // Constantes para resolver issues php:S1192 (Duplicación de literales)
    private const REDIRECT_DASHBOARD = "Location: ?c=Dashboard";
    private const REDIRECT_ROL_READ  = "Location: ?c=Users&a=rolRead";
    private const REDIRECT_USER_READ = "Location: ?c=Users&a=userRead";

    public function __construct() {
        $this->session = $_SESSION['session'] ?? null;
    }

    // Controlador Principal
    public function main() {
        header(self::REDIRECT_DASHBOARD);
        exit;
    }

    // Controlador Crear Rol
    public function rolCreate() {
        if ($this->session == 'admin') {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                require_once "views/modules/users/rol_create.view.php";
            }
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $rol = new User;
                $rol->setRolName($_POST['rol_name'] ?? '');
                $rol->create_rol();
                header(self::REDIRECT_ROL_READ);
                exit;
            }
        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Controlador Consultar Roles
    public function rolRead() {
        if ($this->session == 'admin') {
            $roles = new User;
            $roles = $roles->read_roles();
            require_once "views/modules/users/rol_read.view.php";
        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Controlador Actualizar Rol
    public function rolUpdate() {
        if ($this->session == 'admin') {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $rolId = new User;
                $rolId = $rolId->getrol_bycode($_GET['idRol'] ?? '');
                require_once "views/modules/users/rol_update.view.php";
            }
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $rolUpdate = new User;
                $rolUpdate->setRolCode($_POST['rol_code'] ?? '');
                $rolUpdate->setRolName($_POST['rol_name'] ?? '');
                $rolUpdate->update_rol();
                header(self::REDIRECT_ROL_READ);
                exit;
            }
        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Controlador Eliminar Rol
    public function rolDelete() {
        if ($this->session == 'admin') {
            $rol = new User;
            $rol->delete_rol($_GET['idRol'] ?? '');
            header(self::REDIRECT_ROL_READ);
            exit;
        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Controlador Crear Usuario
    public function userCreate() {
        if ($this->session == 'admin' || $this->session == 'seller') {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $roles = new User;
                $roles = $roles->read_roles();
                require_once "views/modules/users/user_create.view.php";
            }
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $user = new User(
                    $_POST['rol_code'] ?? '',
                    null,
                    $_POST['user_name'] ?? '',
                    $_POST['user_lastname'] ?? '',
                    $_POST['user_id'] ?? '',
                    $_POST['user_email'] ?? '',
                    $_POST['user_pass'] ?? '',
                    $_POST['user_state'] ?? ''
                );
                $user->create_user();
                header(self::REDIRECT_USER_READ);
                exit;
            }
        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Controlador Consultar Usuarios
    public function userRead() {
        if ($this->session == 'admin' || $this->session == 'seller') {
            $users = new User;
            $users = $users->read_users();
            require_once "views/modules/users/user_read.view.php";
        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Controlador Actualizar Usuario
    public function userUpdate() {
        if ($this->session == 'admin' || $this->session == 'seller') {
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                $roles = new User;
                $roles = $roles->read_roles();
                $user = new User;
                $user = $user->getuser_bycode($_GET['idUser'] ?? '');
                require_once "views/modules/users/user_update.view.php";
            }
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $userUpdate = new User(
                    $_POST['rol_code'] ?? '',
                    $_POST['user_code'] ?? '',
                    $_POST['user_name'] ?? '',
                    $_POST['user_lastname'] ?? '',
                    $_POST['user_id'] ?? '',
                    $_POST['user_email'] ?? '',
                    $_POST['user_pass'] ?? '',
                    $_POST['user_state'] ?? ''
                );
                $userUpdate->update_user();
                header(self::REDIRECT_USER_READ);
                exit;
            }
        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }

    // Controlador Eliminar Usuario
    public function userDelete() {
        if ($this->session == 'admin') {
            $user = new User;
            $user->delete_user($_GET['idUser'] ?? '');
            header(self::REDIRECT_USER_READ);
            exit;
        } else {
            header(self::REDIRECT_DASHBOARD);
            exit;
        }
    }
}
