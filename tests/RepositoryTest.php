<?php

namespace Neat\Object\Test;

use Neat\Database\Result;
use Neat\Object\EntityTrait;
use Neat\Object\Repository;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use Neat\Object\Test\Helper\UserGroup;
use Neat\Object\Test\Helper\Weirdo;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /**
     * @var Factory
     */
    private $create;

    public function setUp()
    {
        $this->create = new Factory($this);
        EntityTrait::setEntityManager($this->create->entityManager());
    }

    private function repository($entity)
    {
        return $this->create->repository($entity);
    }

//    public function testFindOne()
//    {
//        $where = ['where' => 'constraint'];
//        $orderBy = 'where';
//
//        $mock = $this->create->mockedRepository(User::class, null);
//
//        $mock->expects($this->at(0));
//
//        $this->userRepository->findOne($where, $orderBy);Equals
//    }

    public function testTableName()
    {
        $this->assertSame('user', $this->repository(User::class)->getTableName());
        $this->assertSame(Weirdo::getTableName(), $this->repository(Weirdo::class)->getTableName());
    }

    public function testIdentifier()
    {
        $this->assertSame('id', $this->repository(User::class)->getIdentifier());
        $this->assertSame(Weirdo::getIdentifier(), $this->repository(Weirdo::class)->getIdentifier());
        $this->assertSame(UserGroup::getIdentifier(), $this->repository(UserGroup::class)->getIdentifier());
    }

        $this->assertEquals(Weirdo::getIdentifier(), $this->create->repository(Weirdo::class)->getIdentifier());
    }

    public function testFindById()
    {
        $this->assertTrue(true, "@TODO");
    }

    public function testFindAll()
    {
        $this->assertTrue(true, "@TODO");
    }

//    public function testQuery()
//    {
//
//    }
}
