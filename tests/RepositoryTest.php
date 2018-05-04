<?php

namespace Neat\Object\Test;

use Neat\Object\Repository;
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
