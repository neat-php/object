<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Relations\Reference\JunctionTable;
use Neat\Object\Repository;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\Group;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class JunctionTableTest extends TestCase
{
    /**
     * @var JunctionTable
     */
    private $key;

    /**
     * Setup before each test method
     */
    public function setUp()
    {
        $factory    = new Factory;
        $policy     = new Policy;
        $connection = $factory->connection();
        $localKey   = new Property(new ReflectionProperty(User::class, 'id'));
        $remoteKey  = new Property(new ReflectionProperty(Group::class, 'id'));
        $properties = $policy->properties(Group::class);
        $repository = new Repository(
            $connection,
            Group::class,
            $policy->table(Group::class),
            ['id'],
            $properties
        );

        $this->key = new JunctionTable(
            $localKey,
            $remoteKey,
            'id',
            $repository,
            $connection,
            'group_user',
            'user_id',
            'group_id'
        );
    }

    /**
     * Test load
     */
    public function testLoad()
    {
        $user = new User;
        $load = $this->key->load($user);
        $this->assertInternalType('array', $load);
        $this->assertCount(0, $load);

        $user     = new User;
        $user->id = 1;

        $load = $this->key->load($user);
        $this->assertInternalType('array', $load);
        $this->assertCount(2, $load);
        $this->assertInstanceOf(Group::class, array_shift($load));
    }

    /**
     * Test store
     */
    public function testStore()
    {
        $user       = new User;
        $user->id   = 4;
        $groupA     = new Group;
        $groupA->id = 1;
        $groupB     = new Group;
        $groupB->id = 2;

        $this->key->store($user, [$groupA]);
        $load = $this->key->load($user);
        $this->assertInternalType('array', $load);
        $this->assertCount(1, $load);
        $this->assertInstanceOf(Group::class, array_shift($load));

        $this->key->store($user, [$groupA, $groupB]);
        $load = $this->key->load($user);
        $this->assertInternalType('array', $load);
        $this->assertCount(2, $load);
        $this->assertInstanceOf(Group::class, array_shift($load));

        $this->key->store($user, [$groupA]);
        $load = $this->key->load($user);
        $this->assertInternalType('array', $load);
        $this->assertCount(1, $load);
        $this->assertInstanceOf(Group::class, array_shift($load));
    }
}
