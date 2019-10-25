<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Manager;
use Neat\Object\Property;
use Neat\Object\ReferenceFactory;
use Neat\Object\Relations\Reference;
use Neat\Object\Relations\Reference\JunctionTable;
use Neat\Object\Relations\Reference\LocalKey;
use Neat\Object\Relations\Reference\RemoteKey;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\Group;
use Neat\Object\Test\Helper\ReferenceFactoryMock;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class ReferenceFactoryTest extends TestCase
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var ReferenceFactory
     */
    private $factory;

    /**
     * Setup before each test method
     */
    protected function setUp()
    {
        $factory       = new Factory;
        $this->manager = $factory->manager();
        $this->factory = new ReferenceFactoryMock($this->manager);
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Get property access
     *
     * @param string $class
     * @param string $property
     * @return Property
     */
    private function property(string $class, string $property): Property
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return new Property(new ReflectionProperty($class, $property));
    }

    /**
     * Test junctionTable factory
     */
    public function testJunctionTable()
    {
        $reference = $this->factory->junctionTable(User::class, Group::class);
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertInstanceOf(JunctionTable::class, $reference);

        $this->assertAttributeEquals($this->property(User::class, 'id'), 'localKey', $reference);
        $this->assertAttributeEquals($this->property(Group::class, 'id'), 'remoteKey', $reference);
        $this->assertAttributeSame('id', 'remoteKeyString', $reference);
        $this->assertAttributeSame($this->manager->repository(Group::class), 'remoteRepository', $reference);
        $this->assertAttributeSame($this->manager->connection(), 'connection', $reference);
        $this->assertAttributeSame('group_user', 'table', $reference);
        $this->assertAttributeSame('user_id', 'localForeignKey', $reference);
        $this->assertAttributeSame('group_id', 'remoteForeignKey', $reference);
    }

    /**
     * Test localKey factory
     */
    public function testLocalKey()
    {
        $reference = $this->factory->localKey(Address::class, User::class);
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertInstanceOf(LocalKey::class, $reference);

        $this->assertAttributeEquals($this->property(Address::class, 'userId'), 'localForeignKey', $reference);
        $this->assertAttributeEquals($this->property(User::class, 'id'), 'remoteKey', $reference);
        $this->assertAttributeSame('id', 'remoteKeyString', $reference);
        $this->assertAttributeSame($this->manager->repository(User::class), 'remoteRepository', $reference);
    }

    /**
     * Test remoteKey factory
     */
    public function testRemoteKey()
    {
        $reference = $this->factory->remoteKey(User::class, Address::class);
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertInstanceOf(RemoteKey::class, $reference);

        $this->assertAttributeEquals($this->property(User::class, 'id'), 'localKey', $reference);
        $this->assertAttributeEquals($this->property(Address::class, 'userId'), 'remoteForeignKey', $reference);
        $this->assertAttributeSame('user_id', 'remoteKey', $reference);
        $this->assertAttributeSame($this->manager->repository(Address::class), 'remoteRepository', $reference);
    }
}
