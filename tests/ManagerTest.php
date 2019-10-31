<?php

namespace Neat\Object\Test;

use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\Test\Helper\Factory;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ManagerTest extends TestCase
{
    use Factory;

    /**
     * Test connection getter
     */
    public function testConnection()
    {
        $connection = $this->connection();
        $policy     = $this->policy();
        $manager    = new Manager($connection, $policy);

        $this->assertSame($connection, $manager->connection());
    }

    /**
     * Test policy getter
     */
    public function testPolicy()
    {
        $connection = $this->connection();
        $policy     = $this->policy();
        $manager    = new Manager($connection, $policy);

        $this->assertSame($policy, $manager->policy());
    }

    /**
     * Test get after unset
     */
    public function testGetAfterUnset()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Object manager not set: default');

        Manager::unset();
        $this->assertFalse(Manager::isset());
        Manager::get();
    }

    /**
     * Test get, set and deprecated instance method
     */
    public function testGetAndSet()
    {
        Manager::set($manager = $this->manager());

        $this->assertTrue(Manager::isset());
        $this->assertSame($manager, Manager::get());
        $this->assertSame($manager, Manager::get('default'));
        $this->assertSame($manager, Manager::instance());
        $this->assertSame($manager, Manager::instance('default'));
    }

    /**
     * Test get, set and deprecated instance method
     */
    public function testGetAndSetFactory()
    {
        Manager::setFactory(function () {
            return $this->manager();
        });

        $this->assertTrue(Manager::isset());
        $this->assertInstanceOf(Manager::class, Manager::get());
    }

    /**
     * Test deprecated create method
     */
    public function testCreate()
    {
        Manager::unset();

        $defaultConnection = $this->connection();
        $defaultManager    = Manager::create($defaultConnection, null);

        $customConnection = $this->connection();
        $customPolicy     = $this->policy();
        $customManager    = Manager::create($customConnection, $customPolicy, 'create-custom-test');

        $this->assertNotSame($customManager, $defaultManager);

        $this->assertTrue(Manager::isset());
        $this->assertSame($defaultManager, Manager::get());
        $this->assertSame($defaultConnection, Manager::get()->connection());
        $this->assertInstanceOf(Policy::class, Manager::get()->policy());

        $this->assertSame($customManager, Manager::get('create-custom-test'));
        $this->assertSame($customConnection, Manager::get('create-custom-test')->connection());
        $this->assertSame($customPolicy, Manager::get('create-custom-test')->policy());
    }
}
