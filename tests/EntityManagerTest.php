<?php

namespace Neat\Object\Test;

use Neat\Object\EntityManager;
use Neat\Object\Test\Helper\Factory;
use PHPUnit\Framework\TestCase;

class EntityManagerTest extends TestCase
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
        $this->assertEquals($this->create->connection(), $this->create->entityManager()->getConnection());
    }

    public function testInstance()
    {
        $entityManager = EntityManager::instance();

        $this->assertInstanceOf(EntityManager::class, $entityManager);
    }

    public function testCreateCustom()
    {
        $connection = $this->create->connection();
        $entityManager = EntityManager::create($connection, 'create-custom-test');
        $this->assertNotSame($entityManager, EntityManager::instance());
        $this->assertSame($entityManager, EntityManager::instance('create-custom-test'));
    }
}
