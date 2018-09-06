<?php

namespace Neat\Object\Test;

use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\Test\Helper\Factory;
use PHPUnit\Framework\TestCase;

class ManagerTest extends TestCase
{
    /**
     * @var Factory
     */
    private $create;

    /**
     * Setup before each test method
     */
    public function setUp()
    {
        $this->create = new Factory;
    }

    /**
     * Test connection getter
     */
    public function testConnection()
    {
        $this->assertEquals($this->create->connection(), $this->create->manager()->connection());
    }

    /**
     * Test policy getter
     */
    public function testPolicy()
    {
        $this->assertEquals(new Policy, $this->create->manager()->policy());
    }

    /**
     * Test static manager getter
     */
    public function testInstance()
    {
        $manager = Manager::instance();

        $this->assertInstanceOf(Manager::class, $manager);
    }

    /**
     * Test create custom
     */
    public function testCreateCustom()
    {
        $connection    = $this->create->connection();
        $customManager = Manager::create($connection, null, 'create-custom-test');

        $this->assertNotSame($customManager, Manager::instance());
        $this->assertSame($customManager, Manager::instance('create-custom-test'));
    }

    /**
     * Test custom policy
     */
    public function testCustomPolicy()
    {
        $connection = $this->create->connection();
        $policy     = new Policy;
        $manager    = new Manager($connection, $policy);

        $this->assertSame($policy, $manager->policy());
    }
}
