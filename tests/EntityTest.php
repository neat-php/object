<?php

namespace Neat\Object\Test;

use Neat\Object\Collection;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use Neat\Object\Test\Helper\UserGroup;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    /**
     * Factory
     *
     * @var Factory
     */
    private $create;

    public static function setUpBeforeClass()
    {

    }

    public function setUp()
    {
        $this->create  = new Factory($this);
    }

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

    public function testFindById()
    {
        $user = User::findById(1);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John', $user->firstName);
        $user = User::findById(0);
        $this->assertNull($user);
    }

    public function testFindOne()
    {
        $user = User::findOne(['first_name' => 'John']);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John', $user->firstName);
        $user = User::findOne(['first_name' => 'not existing']);
        $this->assertNull($user);
    }

    public function testFindAll()
    {
        $users = User::findAll(['id' => 1]);
        $this->assertInstanceOf(Collection::class, $users);
        $user = $users->first();
        $this->assertInstanceOf(User::class, $user);
        $this->assertCount(1, $users);

        $users = User::findAll(['id' => 0]);
        $this->assertInstanceOf(Collection::class, $users);
        $this->assertCount(0, $users);
    }

    public function testExists()
    {
        $userRepository = $this->create->repository(User::class);
        $this->assertTrue($userRepository->exists(3));
        $this->assertFalse($userRepository->exists(4));
    }

    public function testStore()
    {
        $user             = new User;
        $user->username   = 'ffox';
        $user->typeId     = 1;
        $user->firstName  = 'Frank';
        $user->lastName   = 'Fox';
        $user->active     = true;
        $user->updateDate = new \DateTime('today');

        $user->store();
        $this->assertNotNull($user->id);

        $dbUser = User::findById($user->id);
        $this->assertEquals($user, $dbUser);
        $dbUser->active   = false;
        $user->updateDate = new \DateTime('today +1hour');
        $user->store();
        $this->assertSame($user->id, $dbUser->id);

        $userGroup          = new UserGroup;
        $userGroup->userId  = $user->id;
        $userGroup->groupId = 3;
        $userGroup->store();
        $this->assertEquals($user->id, $userGroup->userId);
        $this->assertEquals(3, $userGroup->groupId);
    }
}
