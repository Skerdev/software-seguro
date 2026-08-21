<?php
// tests/unit/ExampleTest.php
require_once __DIR__ . '/../TestCase.php';

class ExampleTest extends TestCase
{
    public function testDatabaseConnection()
    {
        // Verifica si la conexión a la base de datos es del tipo correcto
        $this->assertInstanceOf(PDO::class, $this->pdo);
    }

    public function testBasicAssertions()
    {
        // Verifica aserciones básicas y manipulación simulada de sesión
        $this->assertTrue(true);
        $this->assertEquals(2, 1 + 1);
        $_SESSION['test'] = 'ok';
        $this->assertNotEmpty($_SESSION);
        $this->assertEquals('ok', $_SESSION['test']);
    }

    public function testResetDatabase()
    {
        // Verifica que la base de datos de pruebas se reinicie correctamente con datos iniciales
        $result = resetTestDatabase();
        $this->assertTrue($result);
        
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM ROLES");
        $count = $stmt->fetchColumn();
        $this->assertEquals(3, $count);
    }
}

