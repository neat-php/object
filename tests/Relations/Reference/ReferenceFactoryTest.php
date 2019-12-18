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
        $reference = $this->referenceFactoryMock()->junctionTable(User::class, Group::class);
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertInstanceOf(JunctionTable::class, $reference);

        $expect = new JunctionTable(
            $this->property(User::class, 'id'),
            $this->property(Group::class, 'id'),
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
    public function testLocalKey()
    {
        $reference = $this->referenceFactoryMock()->localKey(Address::class, User::class);
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertInstanceOf(LocalKey::class, $reference);

        $expected = new LocalKey(
            $this->property(Address::class, 'userId'),
            $this->property(User::class, 'id'),
            'id',
            Manager::get()->repository(User::class)
        );
        $this->assertEquals($expected, $reference);
    }

    /**
     * Test remoteKey factory
     */
    public function testRemoteKey()
    {
        $reference = $this->referenceFactoryMock()->remoteKey(User::class, Address::class);
        $this->assertInstanceOf(Reference::class, $reference);

        $expected = new RemoteKey(
            $this->property(User::class, 'id'),
            $this->property(Address::class, 'userId'),
            'user_id',
            Manager::get()->repository(Address::class)
        );
        $this->assertEquals($expected, $reference);
    }
}
