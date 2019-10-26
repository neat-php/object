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
    use Factory;

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Create JunctionTable reference
     *
     * @return JunctionTable
     */
    public function junctionTable(): JunctionTable
    {
        $policy     = new Policy;
        $connection = $this->connection();
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

        return new JunctionTable(
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
        $junctionTable = $this->junctionTable();

        $user = new User;
        $load = $junctionTable->load($user);
        $this->assertInternalType('array', $load);
        $this->assertCount(0, $load);

        $user     = new User;
        $user->id = 1;

        $load = $junctionTable->load($user);
        $this->assertInternalType('array', $load);
        $this->assertCount(2, $load);
        $this->assertInstanceOf(Group::class, array_shift($load));
    }

    /**
     * Test store
     */
    public function testStore()
    {
        $junctionTable = $this->junctionTable();

        $user       = new User;
        $user->id   = 4;
        $groupA     = new Group;
        $groupA->id = 1;
        $groupB     = new Group;
        $groupB->id = 2;

        $junctionTable->store($user, [$groupA]);
        $load = $junctionTable->load($user);
        $this->assertInternalType('array', $load);
        $this->assertCount(1, $load);
        $this->assertInstanceOf(Group::class, array_shift($load));

        $junctionTable->store($user, [$groupA, $groupB]);
        $load = $junctionTable->load($user);
        $this->assertInternalType('array', $load);
        $this->assertCount(2, $load);
        $this->assertInstanceOf(Group::class, array_shift($load));

        $junctionTable->store($user, [$groupA]);
        $load = $junctionTable->load($user);
        $this->assertInternalType('array', $load);
        $this->assertCount(1, $load);
        $this->assertInstanceOf(Group::class, array_shift($load));
    }
}
