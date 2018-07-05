<?php

namespace Neat\Object\Test;

use Neat\Database\Result;
use Neat\Object\Collection;
use Neat\Object\Manager;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use Neat\Object\Test\Helper\UserGroup;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /**
     * @var Factory
     */
    private $create;

    /**
     * @var Manager
     */
    private $manager;

    public static function setUpBeforeClass()
    {
        $factory = new Factory;
        Manager::create($factory->connection(), null, 'repository-test');
    }

    public function setUp()
    {
        $this->create  = new Factory;
        $this->manager = Manager::instance('repository-test');
    }

    public function testFindById()
    {
        $userGroupData       = ['user_id' => 1, 'group_id' => 2];
        $userGroupRepository = $this->manager->repository(UserGroup::class);

        $userGroup = $userGroupRepository->findById($userGroupData);
        $this->assertInstanceOf(UserGroup::class, $userGroup);
        $this->assertEquals(1, $userGroup->userId);
        $this->assertEquals(2, $userGroup->groupId);
    }

    public function testFindByIdSingle()
    {
        $this->expectException(\RuntimeException::class);
        $userRepository = $this->manager->repository(User::class);
        $userRepository->findById([1, 2]);
    }

    public function testFindByIdComposed()
    {
        $this->expectException(\RuntimeException::class);
        $userGroupRepository = $this->manager->repository(UserGroup::class);
        $userGroupRepository->findById('test');
    }

    public function testFindByIdComposedArray()
    {
        $this->expectException(\RuntimeException::class);
        $userGroupRepository = $this->manager->repository(UserGroup::class);
        $userGroupRepository->findById(['test']);
    }

    public function testFindOne()
    {
        $userRepository = $this->manager->repository(User::class);

        $user = $userRepository->findOne('id < 3', 'id DESC');
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(2, $user->id);
    }

    public function testFind()
    {
        $userRepository = $this->manager->repository(User::class);
        $result         = $userRepository->find(null, 'id DESC');
        $this->assertInstanceOf(Result::class, $result);
        $rows = $result->rows();
        $this->assertCount(3, $rows);
        $row = reset($rows);
        $this->assertSame('3', $row['id']);

        $result = $userRepository->find(['active' => false]);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertCount(1, $result->rows());

        $result = $userRepository->find('active = 1');
        $this->assertInstanceOf(Result::class, $result);
        $this->assertCount(2, $result->rows());

        $query = $userRepository->query('u');
        $query->where('active = 1');
        $result = $userRepository->find($query);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertCount(2, $result->rows());

        $result = $userRepository->find('1 = 2');
        $this->assertInstanceOf(Result::class, $result);
        $this->assertCount(0, $result->rows());
    }

    public function testFindAll()
    {
        $userRepository = $this->manager->repository(User::class);
        $users          = $userRepository->findAll(null, 'id DESC');
        $this->assertInternalType('array', $users);
        $this->assertCount(3, $users);
        $user = reset($users);
        $this->assertSame(3, $user->id);

        $users = $userRepository->findAll(['active' => false]);
        $this->assertInternalType('array', $users);
        $this->assertCount(1, $users);
    }

    public function testCollection()
    {
        $userRepository  = $this->manager->repository(User::class);
        $usersCollection = $userRepository->collection();
        $this->assertInstanceOf(Collection::class, $usersCollection);
        $this->assertCount(3, $usersCollection);
        $user = $usersCollection->first();
        $this->assertInstanceOf(User::class, $user);
    }

    public function testIterateAll()
    {
        $userRepository = $this->manager->repository(User::class);

        $this->assertCount(3, $userRepository->iterateAll());
        $i = 1;
        foreach ($userRepository->iterateAll() as $user) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertSame($i++, $user->id);
        }
    }

    public function testCreate()
    {
        $userRepository = $this->manager->repository(User::class);
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
        $user       = $userRepository->fromArray(new User, $data);
        $this->assertEquals($user, $userRepository->findById($id));

        $data['active'] = '0';
        $userRepository->update($data['id'], $data);
    }
}
