<?php

namespace Neat\Object\Test;

use Neat\Database\Result;
use Neat\Object\EntityTrait;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\Group;
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

    public function testFindOne()
    {
        $userRepository = $this->create->repository(User::class);

        $result = $userRepository->findOne('id < 3', 'id DESC');
        $this->assertInstanceOf(Result::class, $result);
        $row = $result->row();
        $this->assertSame('2', $row['id']);
    }

    public function testFindById()
    {
        $userGroupData = ['user_id' => 1, 'group_id' => 2];

        $userGroup = UserGroup::findById($userGroupData);
        $this->assertInstanceOf(UserGroup::class, $userGroup);
        $this->assertEquals(1, $userGroup->userId);
        $this->assertEquals(2, $userGroup->groupId);
    }

    public function testFindByIdSingle()
    {
        $this->expectException(\RuntimeException::class);
        User::findById([1, 2]);
    }

    public function testFindByIdComposed()
    {
        $this->expectException(\RuntimeException::class);
        UserGroup::findById('test');
    }

    public function testFindAll()
    {
        $userRepository = $this->create->repository(User::class);
        $result         = $userRepository->findAll(null, 'id DESC');
        $this->assertInstanceOf(Result::class, $result);
        $rows = $result->rows();
        $this->assertCount(3, $rows);
        $row = array_shift($rows);
        $this->assertSame('3', $row['id']);

        $result = $userRepository->findAll(['active' => false]);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertCount(1, $result->rows());
    }

    public function testGetRemoteIdentifier()
    {
        $this->assertSame('user_id', User::getEntityManager()->getRepository(User::class)->getRemoteIdentifier());
        $this->assertSame('groupid', Group::getEntityManager()->getRepository(Group::class)->getRemoteIdentifier());
    }

//    public function testQuery()
//    {
//
//    }
}
