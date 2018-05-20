<?php

namespace Neat\Object\Test;

use Neat\Object\ArrayCollection;
use Neat\Object\EntityManager;
use Neat\Object\EntityTrait;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use Neat\Object\Test\Helper\UserGroup;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    /**
     * Factory
     *
     * @var Factory
     */
    private $create;

    /**
     * @var EntityManager
     */
    private $manager;

    public function setUp()
    {
        $this->create  = new Factory($this);
        $this->user    = new User;
        $this->manager = $this->create->entityManager();
        EntityTrait::setEntityManager($this->manager);
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

    public function testRepository()
    {
        $this->assertEquals($this->create->entityManager(), User::getEntityManager());
    }

//    public function testQuery()
//    {
//        $this->assertSQL('SELECT * FROM user', User::query()->getSelectQuery());
//    }

    public function testCreateFromArray()
    {
        // We can't use now because it will fail comparing anything smaller than seconds
        $updateDate                 = new \DateTime(date('Y-m-d H:i:s'));
        $array                      = [
            'id'           => 1,
            'type_id'      => null,
            'username'     => 'jdoe',
            'first_name'   => 'John',
            'middle_name'  => null,
            'last_name'    => 'Doe',
            'active'       => 1,
            'update_date'  => $updateDate->format('Y-m-d H:i:s'),
            'deleted_date' => null,
        ];
        $user                       = User::createFromArray($array);
        $objectArray                = $array;
        $objectArray['active']      = true;
        $objectArray['update_date'] = $updateDate;
        $this->assertEquals(
            array_values(array_merge($objectArray, ['active' => true, 'update_date' => $updateDate])),
            [
                $user->id,
                $user->typeId,
                $user->username,
                $user->firstName,
                $user->middleName,
                $user->lastName,
                $user->active,
                $user->updateDate,
                $user->deletedDate,
            ],
            'Test object values');
        $this->assertEquals($array, $user->toArray(), 'Test toArray method');
    }

    public function testFindById()
    {
        $user = User::findById(1);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John', $user->firstName);
        $user = User::findById(0);
        $this->assertEquals(null, $user);
    }

    public function testFindAll()
    {
        $users = User::findAll(['id' => 1]);
        $this->assertInstanceOf(ArrayCollection::class, $users);
        $user = $users->first();
        $this->assertInstanceOf(User::class, $user);
        $this->assertCount(1, $users);

        $users = User::findAll(['id' => 0]);
        $this->assertInstanceOf(ArrayCollection::class, $users);
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
