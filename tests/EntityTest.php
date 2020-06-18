<?php

namespace Neat\Object\Test;

use DateTime;
use DateTimeImmutable;
use Generator;
use Neat\Object\Collection;
use Neat\Object\Manager;
use Neat\Object\Query;
use Neat\Object\SQLQuery;
use Neat\Object\Test\Helper\Assertions;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\GroupUser;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    use Assertions;
    use Factory;

    /**
     * Test has
     *
     * @runInSeparateProcess enabled
     */
    public function testHas()
    {
        Manager::set($this->manager());

        $this->assertTrue(User::has(1));
        $this->assertFalse(User::has(0));
    }

    /**
     * Test select
     *
     * @runInSeparateProcess enabled
     */
    public function testSelect()
    {
        Manager::set($this->manager());

        $select = User::select();
        $this->assertInstanceOf(Query::class, $select);
        $this->assertSQL('SELECT `user`.* FROM `user`', $select->getQuery());

        $select = User::select('u');
        $this->assertInstanceOf(Query::class, $select);
        $this->assertSQL('SELECT u.* FROM `user` u', $select->getQuery());
    }

    /**
     * Test query
     *
     * @runInSeparateProcess enabled
     */
    public function testQuery()
    {
        Manager::set($this->manager());

        $this->assertInstanceOf(Query::class, User::query());
        $this->assertSQL('SELECT `user`.* FROM `user`', User::query());

        $select = User::query(['active' => false]);
        $this->assertInstanceOf(Query::class, $select);
        $this->assertSQL('SELECT `user`.* FROM `user` WHERE `active` = \'0\'', $select->getQuery());

        $select = User::query('active = 1');
        $this->assertInstanceOf(Query::class, $select);
        $this->assertSQL('SELECT `user`.* FROM `user` WHERE active = 1', $select->getQuery());
    }

    /**
     * Test query
     *
     * @runInSeparateProcess enabled
     */
    public function testSQL()
    {
        Manager::set($this->manager());

        $this->assertInstanceOf(SQLQuery::class, User::sql('SELECT'));
        $this->assertSame('SELECT * FROM user', User::sql('SELECT * FROM user')->getQuery());
    }

    /**
     * Test get
     *
     * @runInSeparateProcess enabled
     */
    public function testGet()
    {
        Manager::set($this->manager());

        $user = User::get(1);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John', $user->firstName);
        $user = User::get(0);
        $this->assertNull($user);
    }

    /**
     * Test one
     *
     * @runInSeparateProcess enabled
     */
    public function testOne()
    {
        Manager::set($this->manager());

        $user = User::one(['first_name' => 'John']);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John', $user->firstName);
        $user = User::one(['first_name' => 'not existing']);
        $this->assertNull($user);
    }

    /**
     * Test all
     *
     * @runInSeparateProcess enabled
     */
    public function testAll()
    {
        Manager::set($this->manager());

        $users = User::all();
        self::assertThat($users, new IsType('array'));
        $this->assertCount(3, $users);
        $user = reset($users);
        $this->assertInstanceOf(User::class, $user);

        $users = User::all(['id' => 1]);
        self::assertThat($users, new IsType('array'));
        $user = reset($users);
        $this->assertInstanceOf(User::class, $user);
        $this->assertCount(1, $users);

        $users = User::all(['id' => 0]);
        self::assertThat($users, new IsType('array'));
        $this->assertCount(0, $users);
    }

    /**
     * Test collection
     *
     * @runInSeparateProcess enabled
     */
    public function testCollection()
    {
        Manager::set($this->manager());

        $usersCollection = User::collection();
        $this->assertInstanceOf(Collection::class, $usersCollection);
        $this->assertCount(3, $usersCollection);
        $user = $usersCollection->first();
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Test iterate
     *
     * @runInSeparateProcess enabled
     */
    public function testIterate()
    {
        Manager::set($this->manager());

        $this->assertCount(3, User::iterate());
        /** @var User $user */
        $i = 1;
        foreach (User::iterate() as $user) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertSame($i++, $user->id);
        }
        $this->assertInstanceOf(Generator::class, User::iterate());
    }

    /**
     * Test store
     *
     * @runInSeparateProcess enabled
     */
    public function testStore()
    {
        Manager::set($this->manager());

        $user               = new User();
        $user->username     = 'ffox';
        $user->typeId       = 1;
        $user->firstName    = 'Frank';
        $user->lastName     = 'Fox';
        $user->active       = true;
        $user->registerDate = new DateTimeImmutable('2019-01-02 12:30:00');
        $user->updateDate   = new DateTime('today');

        $user->store();
        $this->assertNotNull($user->id);

        $dbUser = User::get($user->id);
        $dbUser->relations();
        $this->assertEquals($user, $dbUser);
        $dbUser->active   = false;
        $user->updateDate = new DateTime('today +1 hour');
        $user->store();
        $this->assertSame($user->id, $dbUser->id);

        $groupUser          = new GroupUser();
        $groupUser->userId  = $user->id;
        $groupUser->groupId = 3;
        $groupUser->store();
        $this->assertEquals($user->id, $groupUser->userId);
        $this->assertEquals(3, $groupUser->groupId);
    }

    /**
     *
     * @runInSeparateProcess enabled
     */
    public function testLoad()
    {
        Manager::set($this->manager());

        $dbUser   = User::get(1);
        $user     = new User();
        $user->id = 1;
        $user->load();
        $this->assertEquals($dbUser, $user);
    }

    /**
     * Test delete
     *
     * @runInSeparateProcess enabled
     */
    public function testDelete()
    {
        Manager::set($this->manager());

        $user               = new User();
        $user->username     = 'edejong';
        $user->typeId       = 1;
        $user->firstName    = 'Emma';
        $user->middleName   = 'de';
        $user->lastName     = 'Jong';
        $user->active       = true;
        $user->registerDate = new DateTimeImmutable('2019-01-02 12:30:00');
        $user->updateDate   = new DateTime('yesterday');

        $user->store();
        $user->delete();

        $this->assertNull(User::get($user->id));
    }

    /**
     * Test array conversion
     *
     * @runInSeparateProcess enabled
     */
    public function testArrayConversion()
    {
        Manager::set($this->manager());

        $data = [
            "username"      => 'tdevries',
            "type_id"       => 1,
            "first_name"    => "Thijs",
            "middle_name"   => "de",
            "last_name"     => "Vries",
            "active"        => 1,
            "update_date"   => date("Y-m-d H:i:s"),
            'register_date' => null,
            'deleted_date'  => null,
            'id'            => null,
        ];

        $user = new User();
        $user->fromArray($data);

        $this->assertEquals("Thijs", $user->firstName);
        $this->assertEquals(true, $user->active);
        $this->assertEquals($data, $user->toArray());
    }
}
