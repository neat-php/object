<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Policy;
use Neat\Object\Query;
use Neat\Object\Relations\Reference\LocalKey;
use Neat\Object\Relations\Reference\LocalKeyBuilder;
use Neat\Object\RepositoryInterface;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\Constraint\IsType;
use PHPUnit\Framework\TestCase;

class LocalKeyTest extends TestCase
{
    use Factory;

    /**
     * Create LocalKey reference
     *
     * @return LocalKey
     */
    public function localKey(): LocalKey
    {
        $policy          = new Policy();
        $remoteKey       = $this->propertyInteger(User::class, 'id');
        $localForeignKey = $this->propertyInteger(Address::class, 'userId');

        return new LocalKey($localForeignKey, $remoteKey, 'id', $policy->repository(User::class, $this->connection()));
    }

    /**
     * Test load
     */
    public function testLoad()
    {
        $localKey = $this->localKey();

        $address = new Address();
        $load    = $localKey->load($address);
        self::assertThat($load, new IsType('array'));
        $this->assertCount(0, $load);

        $address         = new Address();
        $address->userId = 1;

        $load = $localKey->load($address);
        $this->assertCount(1, $load);
        $user = array_shift($load);
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Test store
     */
    public function testStore()
    {
        $localKey = $this->localKey();

        $address = new Address();
        $localKey->store($address, []);
        $this->assertSame(null, $address->userId);

        $user     = new User();
        $user->id = 1;
        $localKey->store($address, [$user]);
        $this->assertSame(1, $address->userId);
    }

    public function testSelect()
    {
        $repository = $this->getMockForAbstractClass(RepositoryInterface::class);
        $query      = $this->getMockBuilder(Query::class)->disableOriginalConstructor()
            ->setMethods(['where'])->getMock();
        $query->expects($this->once())->method('where')->with(['id' => 1])->willReturnSelf();
        $repository->expects($this->at(0))->method('select')->with()->willReturn($query);
        $localKey = $this->localKeyFactory(Address::class, User::class)->setRemoteRepository($repository)->resolve();

        $address         = new Address();
        $address->userId = 1;
        $localKey->select($address);
    }

    public function localKeyFactory(string $local, string $remote)
    {
        return new LocalKeyBuilder($this->manager(), $local, $remote);
    }
}
