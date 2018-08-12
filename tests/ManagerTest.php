<?php

namespace Neat\Object\Test;

use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\Test\Helper\Factory;
use PHPUnit\Framework\TestCase;

/**
 * Class ManagerTest
 *
 * @todo add test with custom policy
 *
 * @package Neat\Object\Test
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

    public function testGetConnection()
    {
        $this->assertEquals($this->create->connection(), $this->create->manager()->getConnection());
    }

    public function testGetPolicy()
    {
        $this->assertEquals(new Policy, $this->create->manager()->getPolicy());
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
        $connection    = $this->create->connection();
        $policy = new class extends Policy {};
        $manager = new Manager($connection, $policy);
        $this->assertNotEquals(new Policy(), $manager->getPolicy());
        $this->assertEquals($policy, $manager->getPolicy());
    }
}
