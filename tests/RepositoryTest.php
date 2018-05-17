<?php

namespace Neat\Object\Test;

use Neat\Database\Result;
use Neat\Object\EntityTrait;
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
        $this->assertSame('user', $this->callMethod(User::class, 'repository')->getTableName());
        $this->assertSame('user', $this->getProperty($this->callMethod(User::class, 'repository'), 'tableName'));
        $this->assertSame('weirdo', $this->callMethod(Weirdo::class, 'repository')->getTableName());
        $this->assertSame(
            Weirdo::getTableName(), $this->getProperty($this->callMethod(Weirdo::class, 'repository'), 'tableName')
        );
    }

    public function testIdentifier()
    {
        $userRepository      = $this->callMethod(User::class, 'repository');
        $weirdoRepository    = $this->callMethod(Weirdo::class, 'repository');
        $userGroupRepository = $this->callMethod(UserGroup::class, 'repository');
        $this->assertSame('id', $this->getProperty($userRepository, 'identifier'));
        $this->assertSame(Weirdo::getIdentifier(), $this->getProperty($weirdoRepository, 'identifier'));
        $this->assertSame(UserGroup::getIdentifier(), $this->getProperty($userGroupRepository, 'identifier'));
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

//    public function testQuery()
//    {
//
//    }

    private function callMethod($class, $method, ...$arguments)
    {
        $reflectionClass  = new \ReflectionClass($class);
        $reflectionMethod = $reflectionClass->getMethod($method);
        $reflectionMethod->setAccessible(true);

        if (is_object($class)) {
            return $reflectionMethod->invoke($class, $arguments);
        }

        return $reflectionMethod->invoke(null, $arguments);
    }

    private function getProperty($class, $property)
    {
        $reflectionClass = new \ReflectionClass($class);

        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);

        if (is_object($class)) {
            return $reflectionProperty->getValue($class);
        }

        return $reflectionProperty->getValue();
    }
}
