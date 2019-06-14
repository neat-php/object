<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Relations\Reference\Diff;
use Neat\Object\Relations\Reference\RemoteKey;
use Neat\Object\Repository;
use Neat\Object\RepositoryInterface;
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

    /**
     * @var RepositoryInterface
     */
    private $remoteRepository;

    /**
     * Setup before each test method
     */
    public function setUp()
    {
        $factory          = new Factory;
        $policy           = new Policy;
        $localKey         = new Property(new ReflectionProperty(User::class, 'id'));
        $remoteForeignKey = new Property(new ReflectionProperty(Address::class, 'userId'));
        $properties       = $policy->properties(Address::class);

        $this->remoteRepository = new Repository(
            $factory->connection(),
            Address::class,
            $policy->table(Address::class),
            ['id'],
            $properties
        );
        $this->key              = new RemoteKey($localKey, $remoteForeignKey, 'user_id', $this->remoteRepository);
    }

    /**
     * Test store
     */
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

    public function testDiff()
    {
        $address1      = new Address;
        $address1->id  = 1;
        $address1B     = new Address;
        $address1B->id = '1';
        $address2      = new Address;
        $address2->id  = 2;
        $address3      = new Address;
        $address3->id  = 3;
        $diff          = new Diff($this->remoteRepository, [$address1, $address3], [$address1B, $address2]);
        $this->assertEquals([$address3], $diff->getDelete());
        $this->assertEquals([$address2], $diff->getInsert());
        $this->assertEquals([$address1], $diff->getUpdate());
    }

    /**
     * Test load
     */
    public function testLoad()
    {
        $user = new User;
        /** @var Address[] $load */
        $load = $this->key->load($user);
        $this->assertInternalType('array', $load);

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
