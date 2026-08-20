<?php
// tests/TestCase.php
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        parent::setUp();
        // Iniciar sesión simulada para cada prueba
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        
        // Obtener conexión a la base de datos de prueba e iniciar transacción
        $this->pdo = DataBase::connection();
        $this->pdo->beginTransaction();
    }

    protected function tearDown(): void
    {
        // Revertir todos los cambios realizados en el test
        if ($this->pdo && $this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
        $_SESSION = [];
        parent::tearDown();
    }

    // Helper útil para crear un usuario de prueba rápido
    protected function createTestUser($rol = 'admin', $active = true)
    {
        $user = new User();
        $user->setRolCode($rol === 'admin' ? 'R001' : ($rol === 'seller' ? 'R002' : 'R003'));
        $user->setRolName($rol);
        $user->setUserCode('TEST' . rand(100, 999));
        $user->setUserName('Test');
        $user->setUserLastName('User');
        $user->setUserId('99999');
        $user->setUserEmail('test' . rand(100, 999) . '@test.com');
        $user->setUserPass('test123');
        $user->setUserState($active ? 1 : 0);
        return $user;
    }

    // Helper útil para simular que un rol ha iniciado sesión
    protected function loginAs($rol = 'admin')
    {
        $user = new User();
        $user->setRolCode($rol === 'admin' ? 'R001' : ($rol === 'seller' ? 'R002' : 'R003'));
        $user->setRolName($rol);
        $user->setUserCode('U001');
        $user->setUserName('Test');
        $user->setUserLastName('User');
        $user->setUserId('12345');
        $user->setUserEmail($rol . '@test.com');
        $user->setUserPass(sha1($rol . '123'));
        $user->setUserState(1);
        $_SESSION['session'] = $rol;
        $_SESSION['profile'] = serialize($user);
        return $user;
    }
}
