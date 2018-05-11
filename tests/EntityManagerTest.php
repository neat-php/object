<?php

namespace Neat\Object\Test;

use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\Group;
use Neat\Object\Test\Helper\NoEntity;
use Neat\Object\Test\Helper\User;
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

    public function testGetRepository()
    {
        $this->assertEquals(
            $this->create->repository(User::class),
            $this->create->entityManager()->getRepository(User::class)
        );

        $this->assertInstanceOf(Group::REPOSITORY, $this->create->entityManager()->getRepository(Group::class));

        $this->expectException(\RuntimeException::class);
        $this->create->entityManager()->getRepository(NoEntity::class);
    }

    public function testGetConnection()
    {
        $this->assertEquals($this->create->connection(), $this->create->entityManager()->getConnection());
    }
}
