<?php

namespace Neat\Object\Test;

use Neat\Object\Manager;
use Neat\Object\Test\Helper\Factory;
use PHPUnit\Framework\TestCase;

class ManagerTest extends TestCase
{
    use Factory;

    /**
     * Test connection getter
     */
    public function testConnection()
    {
        $this->assertEquals($this->connection(), $this->manager()->connection());
    }

    /**
     * Test policy getter
     */
    public function testPolicy()
    {
        $this->assertEquals($this->policy(), $this->manager()->policy());
    }

    /**
     * Test deprecated instance method
     */
    public function testInstance()
    {
        Manager::set($manager = $this->manager());

        $this->assertSame($manager, Manager::instance());
    }

    /**
     * Test deprecated create method
     */
    public function testCreate()
    {
        $defaultManager = Manager::create($this->connection(), null);
        $customManager  = Manager::create($this->connection(), null, 'create-custom-test');

        $this->assertNotSame($customManager, $defaultManager);
        $this->assertSame($defaultManager, Manager::get());
        $this->assertSame($customManager, Manager::get('create-custom-test'));
    }

    /**
     * Test custom policy
     */
    public function testCustomPolicy()
    {
        $connection = $this->connection();
        $policy     = $this->policy();
        $manager    = new Manager($connection, $policy);

        $this->assertSame($policy, $manager->policy());
    }
}
