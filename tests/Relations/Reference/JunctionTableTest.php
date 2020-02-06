<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Relations\Reference\JunctionTable;
use Neat\Object\Relations\Reference\JunctionTableBuilder;
use Neat\Object\Repository;
use Neat\Object\RepositoryInterface;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\Group;
use Neat\Object\Test\Helper\GroupUser;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\Constraint\IsType;
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
        $policy     = new Policy();
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

        $user = new User();
        $load = $junctionTable->load($user);
        self::assertThat($load, new IsType('array'));
        $this->assertCount(0, $load);

        $user     = new User();
        $user->id = 1;

        $load = $junctionTable->load($user);
        self::assertThat($load, new IsType('array'));
        $this->assertCount(2, $load);
        $this->assertInstanceOf(Group::class, array_shift($load));
    }

    /**
     * Test store
     */
    public function testStore()
    {
        $junctionTable = $this->junctionTable();

        $user       = new User();
        $user->id   = 4;
        $groupA     = new Group();
        $groupA->id = 1;
        $groupB     = new Group();
        $groupB->id = 2;

        $junctionTable->store($user, [$groupA]);
        $load = $junctionTable->load($user);
        self::assertThat($load, new IsType('array'));
        $this->assertCount(1, $load);
        $this->assertInstanceOf(Group::class, array_shift($load));

        $junctionTable->store($user, [$groupA, $groupB]);
        $load = $junctionTable->load($user);
        self::assertThat($load, new IsType('array'));
        $this->assertCount(2, $load);
        $this->assertInstanceOf(Group::class, array_shift($load));

        $junctionTable->store($user, [$groupA]);
        $load = $junctionTable->load($user);
        self::assertThat($load, new IsType('array'));
        $this->assertCount(1, $load);
        $this->assertInstanceOf(Group::class, array_shift($load));
    }


    public function testGetRemoteKeyValue()
    {
        // User
        $user       = new User();
        $user->id   = 1;
        $repository = $this->getMockForAbstractClass(RepositoryInterface::class);
        $repository->expects($this->once())->method('identifier')->with($user)->willReturn($user->id);
        $junctionTable = $this->junctionTableFactory(Group::class, User::class)->setRemoteRepository(
            $repository
        )->resolve();
        $this->assertSame($user->id, $junctionTable->getRemoteKeyValue($user));

        // GroupUser
        $groupUser          = new GroupUser();
        $groupUser->userId  = 1;
        $groupUser->groupId = 2;
        $repository         = $this->getMockForAbstractClass(RepositoryInterface::class);
        $identifier         = ['user_id' => $groupUser->userId, 'group_id' => $groupUser->groupId];
        $repository->expects($this->once())->method('identifier')->with($groupUser)->willReturn($identifier);
        $junctionTable = $this->junctionTableFactory(Group::class, User::class)
            ->setRemoteRepository($repository)->resolve();
        $this->assertSame($identifier, $junctionTable->getRemoteKeyValue($groupUser));
    }

    private function junctionTableFactory(string $local, string $remote)
    {
        return new JunctionTableBuilder(
            $this->manager(),
            $this->policy(),
            $local,
            $remote
        );
    }
}
