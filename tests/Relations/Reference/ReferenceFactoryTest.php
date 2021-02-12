<?php

namespace Neat\Object\Test\Relations\Reference;

use Neat\Object\Manager;
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

    /**
     * Test junctionTable factory
     */
    public function testJunctionTable(): void
    {
        $reference = $this->referenceFactoryMock()->junctionTable('testJunctionTable', User::class, Group::class);
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertInstanceOf(JunctionTable::class, $reference);

        $expect = new JunctionTable(
            $this->propertyInteger(User::class, 'id'),
            $this->propertyInteger(Group::class, 'id'),
            'id',
            Manager::get()->repository(Group::class),
            Manager::get()->connection(),
            'group_user',
            'user_id',
            'group_id'
        );
        $this->assertEquals($expect, $reference);
    }

    /**
     * Test localKey factory
     */
    public function testLocalKey(): void
    {
        $reference = $this->referenceFactoryMock()->localKey('testLocalKey', Address::class, User::class);
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertInstanceOf(LocalKey::class, $reference);

        $expected = new LocalKey(
            $this->propertyInteger(Address::class, 'userId'),
            $this->propertyInteger(User::class, 'id'),
            'id',
            Manager::get()->repository(User::class)
        );
        $this->assertEquals($expected, $reference);
    }

    /**
     * Test remoteKey factory
     */
    public function testRemoteKey(): void
    {
        $reference = $this->referenceFactoryMock()->remoteKey('testRemoteKey', User::class, Address::class);
        $this->assertInstanceOf(Reference::class, $reference);

        $expected = new RemoteKey(
            $this->propertyInteger(User::class, 'id'),
            $this->propertyInteger(Address::class, 'userId'),
            'user_id',
            Manager::get()->repository(Address::class)
        );
        $this->assertEquals($expected, $reference);
    }
}
