<?php

namespace Neat\Object\Test;

use Neat\Object\Manager;
use Neat\Object\ReferenceFactory;
use Neat\Object\Relations\Reference\JunctionTable;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\Group;
use Neat\Object\Test\Helper\ReferenceFactoryMock;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;

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

    protected function setUp()
    {
        $factory       = new Factory;
        $this->manager = $factory->manager();
        $this->factory = new ReferenceFactoryMock($this->manager);
    }

    public function testJunctionTable()
    {
        $junctionTable = $this->factory->junctionTable(User::class, Group::class);
        $this->assertInstanceOf(JunctionTable::class, $junctionTable);

        // @TODO $this->assertAttributeEquals(, 'localKey', $junctionTable);
        // @TODO $this->assertAttributeEquals(, 'remoteKey', $junctionTable);
        $this->assertAttributeSame('id', 'remoteKeyString', $junctionTable);
        $this->assertAttributeSame($this->manager->repository(Group::class), 'remoteRepository', $junctionTable);
        $this->assertAttributeSame($this->manager->connection(), 'connection', $junctionTable);
        $this->assertAttributeSame('group_user', 'table', $junctionTable);
        $this->assertAttributeSame('user_id', 'localForeignKey', $junctionTable);
        $this->assertAttributeSame('group_id', 'remoteForeignKey', $junctionTable);
    }

    public function testLocalKey()
    {
        // @todo
        $this->addToAssertionCount(1);
    }

    public function testRemoteKey()
    {
        // @todo
        $this->addToAssertionCount(1);
    }
}
