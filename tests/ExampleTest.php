<?php

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testDIContainerLoadsSuccessfully()
    {
        // Our bootstrap.php makes the app() function available globally
        $container = app();
        $this->assertInstanceOf(\DI\Container::class, $container);
        
        $logger = app(\Monolog\Logger::class);
        $this->assertInstanceOf(\Monolog\Logger::class, $logger);
    }
    
    public function testDatabaseConnection()
    {
        // Test that our Database class successfully initializes without error
        $db = getDB();
        $this->assertInstanceOf(Database::class, $db);
        $this->assertInstanceOf(PDO::class, $db->getConnection());
    }
}
