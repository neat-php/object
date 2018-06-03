<?php

namespace Neat\Object\Test;

use Neat\Database\Result;
use Neat\Object\EntityManager;
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

    /**
     * @var EntityManager
     */
    private $entityManager;

    public static function setUpBeforeClass()
    {
        $factory = new Factory(new self);
        EntityManager::create($factory->connection(), 'repository-test');
    }

    public function setUp()
    {
        $this->create        = new Factory($this);
        $this->entityManager = EntityManager::instance('repository-test');
    }

    public function testTableName()
    {
        $userRepository   = $this->entityManager->repository(User::class);
        $weirdoRepository = $this->entityManager->repository(Weirdo::class);

        $this->assertSame('user', $this->callMethod(User::class, 'getTableName'));
        $this->assertSame('user', $this->getProperty($userRepository, 'name'));
        $this->assertSame('user_weirdo', $this->callMethod(Weirdo::class, 'getTableName'));
        $this->assertSame('user_weirdo', $this->getProperty($weirdoRepository, 'name'));
    }

    public function testIdentifier()
    {
        $userRepository      = $this->entityManager->repository(User::class);
        $weirdoRepository    = $this->entityManager->repository(Weirdo::class);
        $userGroupRepository = $this->entityManager->repository(UserGroup::class);
        $this->assertSame(['id'], User::getKey());
        $this->assertSame(['id'], $this->getProperty($userRepository, 'key'));
        $this->assertSame(['key'], Weirdo::getKey());
        $this->assertSame(['key'], $this->getProperty($weirdoRepository, 'key'));
        $this->assertSame(['user_id', 'group_id'], UserGroup::getKey());
        $this->assertSame(['user_id', 'group_id'], $this->getProperty($userGroupRepository, 'key'));
    }

    public function testFindOne()
    {
        $userRepository = $this->entityManager->repository(User::class);

        $user = $userRepository->findOne('id < 3', 'id DESC');
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(2, $user->id);
    }

    public function testFindById()
    {
        $userGroupData       = ['user_id' => 1, 'group_id' => 2];
        $userGroupRepository = $this->entityManager->repository(UserGroup::class);

        $userGroup = $userGroupRepository->findById($userGroupData);
        $this->assertInstanceOf(UserGroup::class, $userGroup);
        $this->assertEquals(1, $userGroup->userId);
        $this->assertEquals(2, $userGroup->groupId);
    }

    public function testFindByIdSingle()
    {
        $this->expectException(\RuntimeException::class);
        $userRepository = $this->entityManager->repository(User::class);
        $userRepository->findById([1, 2]);
    }

    public function testFindByIdComposed()
    {
        $this->expectException(\RuntimeException::class);
        $userGroupRepository = $this->entityManager->repository(UserGroup::class);
        $userGroupRepository->findById('test');
    }

    public function testFindByIdComposedArray()
    {
        $this->expectException(\RuntimeException::class);
        $userGroupRepository = $this->entityManager->repository(UserGroup::class);
        $userGroupRepository->findById(['test']);
    }

    public function testFindAll()
    {
        $userRepository = $this->entityManager->repository(User::class);
        $result         = $userRepository->find(null, 'id DESC');
        $this->assertInstanceOf(Result::class, $result);
        $rows = $result->rows();
        $this->assertCount(3, $rows);
        $row = array_shift($rows);
        $this->assertSame('3', $row['id']);

        $result = $userRepository->find(['active' => false]);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertCount(1, $result->rows());
    }

    public function testIterateAll()
    {
        $userRepository = $this->entityManager->repository(User::class);

        $this->assertCount(3, $userRepository->iterateAll());
        $i = 1;
        foreach ($userRepository->iterateAll() as $user) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertSame($i++, $user->id);
        }
    }

    public function testCreate()
    {
        $userRepository = $this->entityManager->repository(User::class);
        $data           = [
            'username'     => 'ttest',
            'type_id'      => '1',
            'first_name'   => 'test_first',
            'middle_name'  => null,
            'last_name'    => 'test_last',
            'active'       => '1',
            'update_date'  => '2018-05-20',
            'deleted_date' => null,
        ];
        $id             = $userRepository->create($data);
        $this->assertNotNull($id);
        $data['id'] = (string)$id;
        $user = $userRepository->fromArray(new User, $data);
        $this->assertEquals($user, $userRepository->findById($id));

        $data['active'] = '0';
        $userRepository->update($data['id'], $data);
    }

//    public function testQuery()
//    {
//
//    }

    private function callMethod($class, $method, ...$arguments)
    {
        return $this->create->callMethod($class, $method, ...$arguments);
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
