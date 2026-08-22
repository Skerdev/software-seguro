<?php
// tests/integration/LoginTest.php

class LoginTest extends TestCase
{
    private function seedUser($email, $plainPassword, $userState, $rolCode = 1)
    {
        $sql = 'INSERT INTO USERS
                    (rol_code, user_code, user_name, user_lastname, user_id, user_email, user_pass, user_state)
                VALUES
                    (:rolCode, :userCode, :userName, :userLastname, :userId, :userEmail, :userPass, :userState)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'rolCode'      => $rolCode,
            'userCode'     => rand(1000, 999999),
            'userName'     => 'Integracion',
            'userLastname' => 'Test',
            'userId'       => (string) rand(100000, 999999),
            'userEmail'    => $email,
            'userPass'     => hash('sha256', $plainPassword),
            'userState'    => $userState,
        ]);
    }

    private function resolveLoginDecision($user)
    {
        if ($user) {
            if ($user->getUserState() != 0) {
                $_SESSION['session'] = $user->getRolName();
                $_SESSION['profile'] = serialize($user);
                return 'OK';
            }
            return 'El Usuario NO está activo';
        }
        return 'Credenciales incorrectas ó el Usuario NO existe';
    }

    public function testLoginAdminExitoso()
    {
        $this->seedUser('admin@test.com', 'admin123', 1, 1);

        $profile = new User(['userEmail' => 'admin@test.com', 'userPass' => 'admin123']);
        $user = $profile->login();
        $decision = $this->resolveLoginDecision($user);

        $this->assertNotFalse($user);
        $this->assertEquals('OK', $decision);
        $this->assertEquals('admin', $_SESSION['session']);
    }

    public function testLoginUsuarioInactivo()
    {
        $this->seedUser('inactivo@test.com', 'clave123', 0, 1);

        $profile = new User(['userEmail' => 'inactivo@test.com', 'userPass' => 'clave123']);
        $user = $profile->login();
        $decision = $this->resolveLoginDecision($user);

        $this->assertEquals('El Usuario NO está activo', $decision);
        $this->assertArrayNotHasKey('session', $_SESSION);
    }

    public function testLoginCredencialesErradas()
    {
        $this->seedUser('valido@test.com', 'claveCorrecta', 1, 1);

        $profile = new User(['userEmail' => 'valido@test.com', 'userPass' => 'claveIncorrecta']);
        $user = $profile->login();
        $decision = $this->resolveLoginDecision($user);

        $this->assertFalse($user);
        $this->assertEquals('Credenciales incorrectas ó el Usuario NO existe', $decision);
        $this->assertArrayNotHasKey('session', $_SESSION);

        $profileInexistente = new User(['userEmail' => 'no_existe@test.com', 'userPass' => 'cualquiera']);
        $userInexistente = $profileInexistente->login();
        $this->assertFalse($userInexistente);
    }
}

