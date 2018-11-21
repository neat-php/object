<?php

/** @noinspection SqlResolve */

namespace Neat\Object\Test;

use Neat\Object\Collection;
use Neat\Object\Manager;
use Neat\Object\Query;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use Neat\Object\Test\Helper\GroupUser;
use Neat\Object\Test\Helper\SoftDelete;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * Minify SQL query by removing unused whitespace
     *
     * @param string $query
     * @return string
     */
    protected function minifySQL($query)
    {
        $replace = [
            '|\s+|m'     => ' ',
            '|\s*,\s*|m' => ',',
            '|\s*=\s*|m' => '=',
        ];

        return preg_replace(array_keys($replace), $replace, $query);
    }

    /**
     * Assert SQL matches expectation
     *
     * Normalizes whitespace to make the tests less fragile
     *
     * @param string $expected
     * @param string $actual
     */
    protected function assertSQL($expected, $actual)
    {
        $this->assertEquals(
            $this->minifySQL($expected),
            $this->minifySQL($actual)
        );
    }

    /**
     * Setup once
     */
    public static function setUpBeforeClass()
    {
        $factory = new Factory;
        Manager::create($factory->connection(), null, 'repository-test');
    }

    /**
     * Setup before each test method
     */
    public function setUp()
    {
        $this->manager = Manager::instance('repository-test');
    }

    /**
     * Test has
     */
    public function testHas()
    {
        $userRepository = $this->manager->repository(User::class);
        $this->assertTrue($userRepository->has(3));
        $this->assertFalse($userRepository->has(4));
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $userGroupData       = ['user_id' => 1, 'group_id' => 2];
        $userGroupRepository = $this->manager->repository(GroupUser::class);

        $userGroup = $userGroupRepository->get($userGroupData);
        $this->assertInstanceOf(GroupUser::class, $userGroup);
        $this->assertEquals(1, $userGroup->userId);
        $this->assertEquals(2, $userGroup->groupId);

        $userRepository = $this->manager->repository(User::class);

        $user = $userRepository->get(1);
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(1, $user->id);
    }

    /**
     * Test get composed identifier with single key
     */
    public function testGetSingle()
    {
        $this->expectException(\RuntimeException::class);
        $userRepository = $this->manager->repository(User::class);
        $userRepository->get([1, 2]);
    }

    /**
     * Test get single identifier with composed key
     */
    public function testGetComposed()
    {
        $this->expectException(\RuntimeException::class);
        $userGroupRepository = $this->manager->repository(GroupUser::class);
        $userGroupRepository->get('test');
    }

    /**
     * Test get composed identifier with mismatched element count
     */
    public function testGetComposedArray()
    {
        $this->expectException(\RuntimeException::class);
        $userGroupRepository = $this->manager->repository(GroupUser::class);
        $userGroupRepository->get(['test']);
    }

    /**
     * Test select
     */
    public function testSelect()
    {
        $repository = $this->manager->repository(User::class);

        $select = $repository->select();
        $this->assertInstanceOf(Query::class, $select);
        $this->assertSQL('SELECT `user`.* FROM `user`', $select->getQuery());

        $select = $repository->select('u');
        $this->assertInstanceOf(Query::class, $select);
        $this->assertSQL('SELECT u.* FROM `user` u', $select->getQuery());
    }

    /**
     * Test query
     */
    public function testQuery()
    {
        $repository = $this->manager->repository(User::class);

        $this->assertInstanceOf(Query::class, $repository->query());
        $this->assertSQL('SELECT `user`.* FROM `user`', $repository->query());

        $select = $repository->query(['active' => false]);
        $this->assertInstanceOf(Query::class, $select);
        $this->assertSQL('SELECT `user`.* FROM `user` WHERE `active` = \'0\'', $select->getQuery());

        $select = $repository->query('active = 1');
        $this->assertInstanceOf(Query::class, $select);
        $this->assertSQL('SELECT `user`.* FROM `user` WHERE active = 1', $select->getQuery());
    }

    /**
     * Test one
     */
    public function testOne()
    {
        $repository = $this->manager->repository(User::class);

        $user = $repository->one('id = 1');
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(1, $user->id);
    }

    /**
     * Test all
     */
    public function testAll()
    {
        $repository = $this->manager->repository(User::class);
        $users      = $repository->all($repository->select()->orderBy('id DESC'));
        $this->assertInternalType('array', $users);
        $this->assertCount(3, $users);
        $user = reset($users);
        $this->assertSame(3, $user->id);

        $users = $repository->all(['active' => false]);
        $this->assertInternalType('array', $users);
        $this->assertCount(1, $users);
    }

    /**
     * Test collection
     */
    public function testCollection()
    {
        $userRepository  = $this->manager->repository(User::class);
        $usersCollection = $userRepository->collection();
        $this->assertInstanceOf(Collection::class, $usersCollection);
        $this->assertCount(3, $usersCollection);
        $user = $usersCollection->first();
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Test iterate
     */
    public function testIterate()
    {
        $userRepository = $this->manager->repository(User::class);

        $this->assertCount(3, $userRepository->iterate());
        $i = 1;
        foreach ($userRepository->iterate() as $user) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertSame($i++, $user->id);
        }
        $this->assertInstanceOf(\Generator::class, $userRepository->iterate());
    }

    /**
     * Test insert and update
     */
    public function testInsertAndUpdate()
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
        $id             = $userRepository->insert($data);
        $this->assertNotNull($id);
        $data['id'] = (string)$id;
        $user       = $userRepository->fromArray(new User, $data);
        $this->assertEquals($user, $userRepository->get($id));

        $data['active'] = '0';
        $userRepository->update($data['id'], $data);
    }

    public function testSoftDelete()
    {
        $delete = new SoftDelete();
        $delete->store();
        $delete->delete();

        $this->assertNotNull($delete->deletedDate, "deletedDate is null");
    }
}
