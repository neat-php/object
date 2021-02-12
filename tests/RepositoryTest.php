<?php

/** @noinspection SqlResolve */

namespace Neat\Object\Test;

use Generator;
use Neat\Object\Collection;
use Neat\Object\Query;
use Neat\Object\Test\Helper\Assertions;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\GroupUser;
use Neat\Object\Test\Helper\Type;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class RepositoryTest extends TestCase
{
    use Assertions;
    use Factory;

    public function testCreate(): void
    {
        $data = [
            'id'   => 1,
            'name' => 'John',
        ];

        $expected = new Type();
        $expected->id = 1;
        $expected->name = 'John';

        $repository = $this->repository(Type::class);

        $this->assertEquals($expected, $repository->create($data));
    }

    /**
     * Test has
     */
    public function testHas(): void
    {
        $repository = $this->repository(User::class);

        $this->assertTrue($repository->has(3));
        $this->assertFalse($repository->has(4));
    }

    /**
     * Test get
     */
    public function testGet(): void
    {
        $userGroupData       = ['user_id' => 1, 'group_id' => 2];
        $userGroupRepository = $this->repository(GroupUser::class);

        $userGroup = $userGroupRepository->get($userGroupData);
        $this->assertInstanceOf(GroupUser::class, $userGroup);
        $this->assertEquals(1, $userGroup->userId);
        $this->assertEquals(2, $userGroup->groupId);

        $userRepository = $this->repository(User::class);

        $user = $userRepository->get(1);
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(1, $user->id);
    }

    /**
     * Test get composed identifier with single key
     */
    public function testGetSingle(): void
    {
        $this->expectException(RuntimeException::class);

        $repository = $this->repository(User::class);
        $repository->get([1, 2]);
    }

    /**
     * Test get single identifier with composed key
     */
    public function testGetComposed(): void
    {
        $this->expectException(RuntimeException::class);

        $repository = $this->repository(GroupUser::class);
        $repository->get('test');
    }

    /**
     * Test get composed identifier with mismatched element count
     */
    public function testGetComposedArray(): void
    {
        $this->expectException(RuntimeException::class);

        $repository = $this->repository(GroupUser::class);
        $repository->get(['test']);
    }

    /**
     * Test select
     */
    public function testSelect(): void
    {
        $repository = $this->repository(User::class);

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
    public function testQuery(): void
    {
        $repository = $this->repository(User::class);

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
    public function testOne(): void
    {
        $repository = $this->repository(User::class);

        $user = $repository->one('id = 1');
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(1, $user->id);
    }

    public function testOneSQL(): void
    {
        $repository = $this->repository(User::class);
        $user       = $repository->sql("SELECT * FROM `user` WHERE id = ?", 1)->one();
        $this->assertInstanceOf(User::class, $user);
        $this->assertSame(1, $user->id);
    }

    /**
     * Test all
     */
    public function testAll(): void
    {
        $repository = $this->repository(User::class);
        $users      = $repository->all($repository->select()->orderBy('id DESC'));
        self::assertThat($users, new IsType('array'));
        $this->assertCount(3, $users);
        $user = reset($users);
        $this->assertSame(3, $user->id);

        $users = $repository->all(['active' => false]);
        self::assertThat($users, new IsType('array'));
        $this->assertCount(1, $users);
    }

    public function testAllSQL(): void
    {
        $repository = $this->repository(User::class);
        $users      = $repository->sql("SELECT * FROM `user` ORDER BY id DESC")->all();
        self::assertThat($users, new IsType('array'));
        $this->assertCount(3, $users);
        $user = reset($users);
        $this->assertSame(3, $user->id);

        $users = $repository->all(['active' => false]);
        self::assertThat($users, new IsType('array'));
        $this->assertCount(1, $users);
    }

    /**
     * Test collection
     */
    public function testCollection(): void
    {
        $userRepository  = $this->repository(User::class);
        $usersCollection = $userRepository->collection();
        $this->assertInstanceOf(Collection::class, $usersCollection);
        $this->assertCount(3, $usersCollection);
        $user = $usersCollection->first();
        $this->assertInstanceOf(User::class, $user);
    }

    public function testCollectionSQL(): void
    {
        $repository      = $this->repository(User::class);
        $usersCollection = $repository->sql("SELECT * FROM `user` ORDER BY id DESC")->collection();
        $this->assertInstanceOf(Collection::class, $usersCollection);
        $this->assertCount(3, $usersCollection);
        $user = $usersCollection->first();
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Test iterate
     */
    public function testIterate(): void
    {
        $userRepository = $this->repository(User::class);

        $this->assertCount(3, $userRepository->iterate());
        $i = 1;
        foreach ($userRepository->iterate() as $user) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertSame($i++, $user->id);
        }
        $this->assertInstanceOf(Generator::class, $userRepository->iterate());
    }

    public function testIterateSQL(): void
    {
        $repository = $this->repository(User::class);

        $userIterator = $repository->sql("SELECT * FROM `user` ORDER BY id DESC")->iterate();
        $this->assertCount(3, $userIterator);
        $i = 1;
        foreach ($repository->iterate() as $user) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertSame($i++, $user->id);
        }
        $this->assertInstanceOf(Generator::class, $repository->iterate());
    }

    /**
     * Test insert and update
     */
    public function testInsertAndUpdate(): void
    {
        $userRepository = $this->repository(User::class);
        $data           = [
            'username'      => 'ttest',
            'type_id'       => '1',
            'first_name'    => 'test_first',
            'middle_name'   => null,
            'last_name'     => 'test_last',
            'active'        => '1',
            'register_date' => '2019-01-05',
            'update_date'   => '2018-05-20',
            'deleted_date'  => null,
        ];
        $id             = $userRepository->insert($data);
        $this->assertNotNull($id);
        $data['id'] = (string) $id;
        $user       = $userRepository->fromArray(new User(), $data);
        $this->assertEquals($user, $userRepository->get($id));

        $data['active'] = '0';
        $userRepository->update($data['id'], $data);
    }

    /**
     * Test load
     */
    public function testLoad(): void
    {
        $repository = $this->repository(User::class);
        /** @var User $user */
        $user            = $repository->get(1);
        $user1           = clone $user;
        $user1->lastName = 'changed';
        $repository->load($user1);
        $this->assertEquals($user, $user1);
        $user = $repository->get(1);
        $this->assertSame($user, $repository->load($user));
    }

    public function testLoadWithoutIdentifier(): void
    {
        $repository = $this->repository(User::class);

        $notPersistedUser           = new User();
        $notPersistedUser->lastName = 'test';
        $user2                      = clone $notPersistedUser;
        $this->assertEquals($user2, $repository->load($notPersistedUser));
    }

    public function testLoadWithNonExistingEntity(): void
    {
        $repository = $this->repository(User::class);

        $notPersistedUser           = new User();
        $notPersistedUser->id       = 1234567890;
        $notPersistedUser->lastName = 'test';
        $user2                      = clone $notPersistedUser;
        $this->assertEquals($user2, $repository->load($notPersistedUser));
    }
}
