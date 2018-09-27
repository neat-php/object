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

    public function testLoad()
    {
        $address         = new Address;
        $address->userId = 1;

        $load = $this->key->load($address);
        $this->assertCount(1, $load);
        $user = array_shift($load);
        $this->assertInstanceOf(User::class, $user);
    }
}
