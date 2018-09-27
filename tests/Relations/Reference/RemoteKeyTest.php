<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Relations\Reference\RemoteKey;
use Neat\Object\Repository;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class RemoteKeyTest extends TestCase
{
    /**
     * @var RemoteKey
     */
    private $key;

    public function setUp()
    {
        $factory          = new Factory;
        $policy           = new Policy;
        $localKey         = new Property(new ReflectionProperty(User::class, 'id'));
        $remoteForeignKey = new Property(new ReflectionProperty(Address::class, 'userId'));
        $properties       = $policy->properties(Address::class);

        $this->key = new RemoteKey($localKey, $remoteForeignKey, 'user_id',
            new Repository($factory->connection(), Address::class, Address::TABLE, ['id'], $properties)
        );
    }

    public function testStore()
    {
        // Insert test
        $user     = new User;
        $user->id = 4;
        $address1 = new Address;
        $this->key->store($user, [$address1]);
        $this->assertNotNull($address1->id);
        $this->assertSame($user->id, $address1->userId);
        $address1->city = 'test';

        // Insert/add and update test
        $address2 = new Address;
        $this->key->store($user, [$address1, $address2]);
        $this->assertNotNull($address2->id);
        $this->assertSame($user->id, $address1->userId);
        $this->assertSame($user->id, $address2->userId);

        $this->assertEquals([$address1, $address2], $this->key->load($user));

        // Delete test
        $this->key->store($user, [$address1]);
        $this->assertEquals([$address1], $this->key->load($user));
    }

    public function testLoad()
    {
        $user     = new User;
        $user->id = 1;
        /** @var Address[] $load */
        $load = $this->key->load($user);
        $this->assertInternalType('array', $load);
        $address = array_shift($load);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertSame(1, $address->userId);
    }
}
