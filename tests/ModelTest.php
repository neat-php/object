<?php

namespace Neat\Object\Test;

use Neat\Object\EntityManager;
use Neat\Object\EntityTrait;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
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
        $this->assertInternalType('array', $users);
        $user = array_shift($users);
        $this->assertInstanceOf(User::class, $user);

        $users = User::findAll(['id' => 0]);
        $this->assertInternalType('array', $users);
    }
}
