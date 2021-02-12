<?php

namespace Neat\Object\Test;

use Neat\Object\Policy;
use Neat\Object\Relations\Reference\LocalKey;
use Neat\Object\Relations\Reference\RemoteKey;
use Neat\Object\Repository;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\GroupUser;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;

class ComposedKeyTest extends TestCase
{
    use Factory;

    /**
     * Create LocalKey reference
     *
     * @return LocalKey
     */
    public function localKey(): LocalKey
    {
        $localForeignKey = $this->propertyInteger(User::class, 'id');
        $remoteKey       = $this->propertyInteger(GroupUser::class, 'userId');

        return new LocalKey($localForeignKey, $remoteKey, 'user_id', $this->repository(GroupUser::class));
    }

    public function testLocalKey(): void
    {
        $user     = new User();
        $user->id = 1;

        $localKey   = $this->localKey();
        $userGroups = $localKey->load($user);
        $userGroup  = array_shift($userGroups);
        $this->assertSame($user->id, $userGroup->userId);
        $this->assertSame(['user_id' => '1', 'group_id' => '1'], $localKey->getRemoteKeyValue($userGroup));
    }

    /**
     * @return Repository
     */
    public function remoteRepository(): Repository
    {
        $policy     = new Policy();
        $properties = $policy->properties(GroupUser::class);

        return new Repository(
            $this->connection(),
            GroupUser::class,
            $policy->table(GroupUser::class),
            ['user_id', 'group_id'],
            $properties
        );
    }

    /**
     * Create remote key
     *
     * @return RemoteKey
     */
    public function remoteKey(): RemoteKey
    {
        $localKey         = $this->propertyInteger(User::class, 'id');
        $remoteForeignKey = $this->propertyInteger(GroupUser::class, 'userId');

        return new RemoteKey($localKey, $remoteForeignKey, 'user_id', $this->remoteRepository());
    }

    public function testRemoteKey(): void
    {
        $user     = new User();
        $user->id = 1;

        $remoteKey  = $this->remoteKey();
        $userGroups = $remoteKey->load($user);
        $userGroup  = array_shift($userGroups);
        $this->assertSame($user->id, $userGroup->userId);
        $this->assertSame(['user_id' => '1', 'group_id' => '1'], $remoteKey->getRemoteKeyValue($userGroup));
        $remoteKey->store($user, []);
        $this->assertSame([], $remoteKey->load($user));
        $remoteKey->store($user, $this->groups($user, [1, 2]));
        $this->assertEquals($this->groups($user, [1, 2]), $remoteKey->load($user));
    }

    private function groups(User $user, array $ids): array
    {
        return array_map(
            function (int $id) use ($user) {
                $group          = new GroupUser();
                $group->userId  = $user->id;
                $group->groupId = $id;

                return $group;
            },
            $ids
        );
    }
}
