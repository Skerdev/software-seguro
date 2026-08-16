<?php
class Login {
    // Definición de constante para evitar duplicación del literal (Regla php:S1192)
    private const VIEW_LOGIN = "views/company/login.view.php";

    public function main() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (!empty($_SESSION['session'])) {
                header("Location: ?c=Dashboard");
                exit;
            }

            $message = "";
            require_once self::VIEW_LOGIN;
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['user_email'] ?? '';
            $pass  = $_POST['user_pass'] ?? '';

            if (empty($email) || empty($pass)) {
                $message = "Por favor ingrese todos los campos.";
                require_once self::VIEW_LOGIN;
                return;
            }

            $profile = new User([
                'userEmail' => $email,
                'userPass' => $pass
            ]);

            $user = $profile->login();

            if ($user) {
                if ($user->getUserState() != 0) {
                    $_SESSION['session'] = $user->getRolName();
                    $_SESSION['profile'] = serialize($user);
                    
                    header("Location: ?c=Dashboard");
                    exit;
                }
                $message = "El Usuario NO está activo";
            } else {
                $message = "Credenciales incorrectas ó el Usuario NO existe";
            }

            require_once self::VIEW_LOGIN;
        }
    }
}