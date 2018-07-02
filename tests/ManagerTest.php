<?php

namespace Neat\Object\Test;

use Neat\Object\Manager;
use Neat\Object\Test\Helper\Factory;
use PHPUnit\Framework\TestCase;

class ManagerTest extends TestCase
{
    /**
     * @var Factory
     */
    private $create;

    public function setUp()
    {
        $this->create = new Factory($this);
    }

    public function testGetConnection()
    {
        $this->assertEquals($this->create->connection(), $this->create->manager()->getConnection());
    }

    public function testInstance()
    {
        $manager = Manager::instance();

        $this->assertInstanceOf(Manager::class, $manager);
    }

    public function testCreateCustom()
    {
        $connection = $this->create->connection();
        $manager    = Manager::create($connection, 'create-custom-test');
        $this->assertNotSame($manager, Manager::instance());
        $this->assertSame($manager, Manager::instance('create-custom-test'));
    }
}
