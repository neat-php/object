<?php

namespace Neat\Object\Test;

use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\Test\Helper\Factory;
use PHPUnit\Framework\TestCase;

/**
 * @todo add test with custom policy
 */
class ManagerTest extends TestCase
{
    /**
     * @var Factory
     */
    private $create;

    public function setUp()
    {
        $this->create = new Factory;
    }

    public function testConnection()
    {
        $this->assertEquals($this->create->connection(), $this->create->manager()->connection());
    }

    public function testPolicy()
    {
        $this->assertEquals(new Policy, $this->create->manager()->policy());
    }

    public function testInstance()
    {
        $manager = Manager::instance();

        $this->assertInstanceOf(Manager::class, $manager);
    }

    public function testCreateCustom()
    {
        $connection    = $this->create->connection();
        $customManager = Manager::create($connection, null, 'create-custom-test');

        $this->assertNotSame($customManager, Manager::instance());
        $this->assertSame($customManager, Manager::instance('create-custom-test'));
    }

    public function testCustomPolicy()
    {
        $connection = $this->create->connection();
        $policy     = new class extends Policy {};
        $manager    = new Manager($connection, $policy);

        $this->assertNotEquals(new Policy(), $manager->policy());
        $this->assertEquals($policy, $manager->policy());
    }
}
