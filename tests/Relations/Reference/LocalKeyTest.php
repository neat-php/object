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
    /**
     * @var LocalKey
     */
    private $key;

    /**
     * Setup before each test method
     */
    public function setUp()
    {
        $factory         = new Factory;
        $policy          = new Policy;
        $remoteKey       = new Property(new ReflectionProperty(User::class, 'id'));
        $localForeignKey = new Property(new ReflectionProperty(Address::class, 'userId'));

        $this->key = new LocalKey($localForeignKey, $remoteKey, 'id',
            $policy->repository(User::class, $factory->connection())
        );
    }

    /**
     * Test load
     */
    public function testLoad()
    {
        $address = new Address;
        $load    = $this->key->load($address);
        $this->assertInternalType('array', $load);
        $this->assertCount(0, $load);

        $address         = new Address;
        $address->userId = 1;

        $load = $this->key->load($address);
        $this->assertCount(1, $load);
        $user = array_shift($load);
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Test store
     */
    public function testStore()
    {
        $address  = new Address;
        $this->key->store($address, []);
        $this->assertSame(null, $address->userId);

        $user     = new User;
        $user->id = 1;
        $this->key->store($address, [$user]);
        $this->assertSame(1, $address->userId);
    }
}
