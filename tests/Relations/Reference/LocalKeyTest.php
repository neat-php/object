<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Relations\Reference\LocalKey;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class LocalKeyTest extends TestCase
{
    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Create LocalKey reference
     *
     * @return LocalKey
     */
    public function localKey(): LocalKey
    {
        $factory         = new Factory;
        $policy          = new Policy;
        $remoteKey       = new Property(new ReflectionProperty(User::class, 'id'));
        $localForeignKey = new Property(new ReflectionProperty(Address::class, 'userId'));

        return new LocalKey($localForeignKey, $remoteKey, 'id',
            $policy->repository(User::class, $factory->connection())
        );
    }

    /**
     * Test load
     */
    public function testLoad()
    {
        $localKey = $this->localKey();

        $address = new Address;
        $load    = $localKey->load($address);
        $this->assertInternalType('array', $load);
        $this->assertCount(0, $load);

        $address         = new Address;
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

        $address  = new Address;
        $localKey->store($address, []);
        $this->assertSame(null, $address->userId);

        $user     = new User;
        $user->id = 1;
        $localKey->store($address, [$user]);
        $this->assertSame(1, $address->userId);
    }
}
