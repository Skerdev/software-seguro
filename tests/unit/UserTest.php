<?php
// tests/unit/UserTest.php
// Pruebas unitarias del modelo User (TC02 - TC05 de la Matriz de Casos de Prueba)

class UserTest extends TestCase
{
    public function testSetRolNameValido()
    {
        $roles = ['admin', 'seller', 'customer'];
        foreach ($roles as $rol) {
            $user = new User();
            $user->setRolName($rol);
            $this->assertEquals($rol, $user->getRolName());
        }
    }

    public function testEmailInvalidoFalla()
    {
        $user = new User();
        $this->expectException(InvalidArgumentException::class);
        $user->setUserEmail('correo_sin_arroba.com');
    }

    public function testEmailValidoEsAceptado()
    {
        $user = new User();
        $user->setUserEmail('usuario@test.com');
        $this->assertEquals('usuario@test.com', $user->getUserEmail());
    }

    public function testUserLimitName()
    {
        $user = new User();
        $this->expectException(InvalidArgumentException::class);
        $user->setUserName('Rumpelstiltskin'); // 16 caracteres
    }

    public function testUserIncorrectNamePattern()
    {
        $user = new User();
        $this->expectException(InvalidArgumentException::class);
        $user->setUserName('Nadia_360');
    }

    public function testUserNamePatternValidoEsAceptado()
    {
        $user = new User();
        $user->setUserName('Albeiro');
        $this->assertEquals('Albeiro', $user->getUserName());
    }
}

