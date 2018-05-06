<?php

namespace Neat\Object\Test;

use Neat\Object\Repository;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use Neat\Object\Test\Helper\Weirdo;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /**
     * @var Factory
     */
    private $create;

    /**
     * @var Repository
     */
    private $userRepository;

    public function setUp()
    {
        $this->create = new Factory($this);
        $this->userRepository = $this->create->repository(User::class);
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
//        $this->userRepository->findOne($where, $orderBy);
//    }

    public function testTableName()
    {
        $this->assertEquals('user', $this->userRepository->getTableName());

        $this->assertEquals(Weirdo::getTableName(), $this->create->repository(Weirdo::class)->getTableName());
    }

    public function testIdentifier()
    {
        $this->assertEquals('id', $this->create->repository(User::class)->getIdentifier());

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
