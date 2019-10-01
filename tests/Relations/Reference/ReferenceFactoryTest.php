<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Manager;
use Neat\Object\Property;
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

/**
 * @runTestsInSeparateProcesses enabled
 */
class ReferenceFactoryTest extends TestCase
{
    use Factory;

    /**
     * Setup before each test method
     */
    protected function referenceFactoryMock(): ReferenceFactoryMock
    {
        Manager::set($this->manager());

        return new ReferenceFactoryMock(Manager::get());
    }

    private function property(string $class, string $property, string $type = 'int'): Property
    {
        return new Property\Integer(new ReflectionProperty($class, $property), $type);
    }

    /**
     * Test junctionTable factory
     */
    public function testJunctionTable()
    {
        $reference = $this->referenceFactoryMock()->junctionTable(User::class, Group::class);
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertInstanceOf(JunctionTable::class, $reference);

        $this->assertAttributeEquals($this->property(User::class, 'id'), 'localKey', $reference);
        $this->assertAttributeEquals($this->property(Group::class, 'id', 'integer'), 'remoteKey', $reference);
        $this->assertAttributeSame('id', 'remoteKeyString', $reference);
        $this->assertAttributeSame(Manager::get()->repository(Group::class), 'remoteRepository', $reference);
        $this->assertAttributeSame(Manager::get()->connection(), 'connection', $reference);
        $this->assertAttributeSame('group_user', 'table', $reference);
        $this->assertAttributeSame('user_id', 'localForeignKey', $reference);
        $this->assertAttributeSame('group_id', 'remoteForeignKey', $reference);
    }

    /**
     * Test localKey factory
     */
    public function testLocalKey()
    {
        $reference = $this->referenceFactoryMock()->localKey(Address::class, User::class);
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertInstanceOf(LocalKey::class, $reference);

        $this->assertAttributeEquals($this->property(Address::class, 'userId'), 'localForeignKey', $reference);
        $this->assertAttributeEquals($this->property(User::class, 'id'), 'remoteKey', $reference);
        $this->assertAttributeSame('id', 'remoteKeyString', $reference);
        $this->assertAttributeSame(Manager::get()->repository(User::class), 'remoteRepository', $reference);
    }

    /**
     * Test remoteKey factory
     */
    public function testRemoteKey()
    {
        $reference = $this->referenceFactoryMock()->remoteKey(User::class, Address::class);
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertInstanceOf(RemoteKey::class, $reference);

        $this->assertAttributeEquals($this->property(User::class, 'id'), 'localKey', $reference);
        $this->assertAttributeEquals($this->property(Address::class, 'userId'), 'remoteForeignKey', $reference);
        $this->assertAttributeSame('user_id', 'remoteKey', $reference);
        $this->assertAttributeSame(Manager::get()->repository(Address::class), 'remoteRepository', $reference);
    }
}
