<?php
// tests/unit/DatabaseTest.php
// Prueba unitaria de conexión (TC01 de la Matriz de Casos de Prueba)

class DatabaseTest extends TestCase
{
    /**
     * TC01 - Verifica que la conexión se establezca usando las variables de
     * entorno de pruebas inyectadas por phpunit.xml (DB_NAME=db_inventory_test)
     * y que retorne una instancia válida de PDO.
     */
    public function testConnection()
    {
        $conn = DataBase::connection();
        $this->assertInstanceOf(PDO::class, $conn);
    }
}

