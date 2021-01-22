<?php

namespace Neat\Object\Test;

use Neat\Object\Decorator\CreatedAt;
use Neat\Object\Decorator\EventDispatcher;
use Neat\Object\Decorator\SoftDelete;
use Neat\Object\Decorator\UpdatedAt;
use Neat\Object\Exception\ClassNotFoundException;
use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Repository;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Events;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\Group;
use Neat\Object\Test\Helper\GroupUser;
use Neat\Object\Test\Helper\HardDelete;
use Neat\Object\Test\Helper\NoEntity;
use Neat\Object\Test\Helper\TimeStamps;
use Neat\Object\Test\Helper\Type;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionProperty;
use RuntimeException;

class PolicyTest extends TestCase
{
    use Factory;

    /**
     * Test class not found exception
     */
    public function testClassNotFoundException()
    {
        $connection = $this->connection();
        $policy     = new Policy();

        $this->expectException(ClassNotFoundException::class);

        $policy->repository('ThisClassDoesNotExist', $connection);
    }

    /**
     * Test factory method
     */
    public function testFactory()
    {
        $policy = new Policy();
        $this->assertNull($policy->factory(User::class));
        $this->assertSame([Type::class, 'createFromArray'], $policy->factory(Type::class));
    }

    /**
     * Create property
     *
     * @param string $name
     * @return Property
     */
    public function createProperty($name)
    {
        $reflection = new ReflectionProperty(User::class, $name);

        return new Property($reflection);
    }

    /**
     * Provide columns
     *
     * @return array
     */
    public function provideColumns()
    {
        return [
            ['id', 'id'],
            ['middleName', 'middle_name'],
        ];
    }

    /**
     * Test column
     *
     * @dataProvider provideColumns
     * @param string $name
     * @param string $column
     */
    public function testColumn(string $name, string $column)
    {
        $policy   = new Policy();
        $property = $this->createProperty($name);

        $this->assertSame($column, $policy->column($property->name()));
    }

    /**
     * Provide foreign keys
     *
     * @return array
     */
    public function provideForeignKeys()
    {
        return [
            [User::class, 'user_id'],
            [Group::class, 'group_id'],
        ];
    }

    /**
     * Test foreign key
     *
     * @dataProvider provideForeignKeys
     * @param string $class
     * @param string $foreignKey
     */
    public function testForeignKey(string $class, string $foreignKey)
    {
        $policy = new Policy();

        $this->assertEquals($foreignKey, $policy->foreignKey($class));
    }

    /**
     * Provide tables
     *
     * @return array
     */
    public function provideTables()
    {
        return [
            ['User', 'user'],
            ['App\\User', 'user'],
            ['\\App\\User', 'user'],
            ['User\\User', 'user'],
            ['user', 'user'],
            ['UserGroup', 'user_group'],
            ['UserGroupTest', 'user_group_test'],
        ];
    }

    /**
     * Test table
     *
     * @dataProvider provideTables
     * @param string $entity
     * @param string $table
     */
    public function testTable(string $entity, string $table)
    {
        $policy = new Policy();

        $this->assertSame($table, $policy->table($entity));
    }

    /**
     * Provide junction tables
     *
     * @return array
     */
    public function provideJunctionTables()
    {
        return [
            [User::class, Group::class, 'group_user'],
            [Group::class, User::class, 'group_user'],
        ];
    }

    /**
     * Test junction table
     *
     * @dataProvider provideJunctionTables
     * @param string $owner
     * @param string $owned
     * @param string $junctionTable
     */
    public function testJunctionTable($owner, $owned, $junctionTable)
    {
        $policy = new Policy();

        $this->assertEquals($junctionTable, $policy->junctionTable($owner, $owned));
    }

    /**
     * Provide skips
     *
     * @return array
     */
    public function provideSkips()
    {
        return [
            ['id', false],
            ['ignored', true],
        ];
    }

    /**
     * Test skip
     *
     * @dataProvider provideSkips
     * @param string $name
     * @param bool   $skip
     */
    public function testSkip(string $name, bool $skip)
    {
        $property = $this->createProperty($name);
        $policy   = new Policy();

        $this->assertSame($skip, $policy->skip($property));
    }

    /**
     * Test created stamp
     */
    public function testCreatedStamp()
    {
        $policy = new Policy();

        $this->assertSame('created_at', $policy->createdStamp(TimeStamps::class));
        $this->assertNull($policy->createdStamp(User::class));
    }

    /**
     * Test updated stamp
     */
    public function testUpdatedStamp()
    {
        $policy = new Policy();

        $this->assertSame('updated_at', $policy->updatedStamp(TimeStamps::class));
        $this->assertNull($policy->updatedStamp(User::class));
    }

    /**
     * Test soft delete
     */
    public function testSoftDelete()
    {
        $policy = new Policy();

        $this->assertSame("deleted_at", $policy->softDelete(TimeStamps::class));
        $this->assertNull($policy->softDelete(HardDelete::class));
    }

    /**
     * Test events
     */
    public function testEvents()
    {
        $policy = new Policy();

        $this->assertSame(Events::EVENTS, $policy->events(Events::class));
        $this->assertSame([], $policy->events(Address::class));
    }

    /**
     * Provide keys
     *
     * @return array
     */
    public function provideKeys()
    {
        return [
            [User::class, ['id']],
            [GroupUser::class, ['user_id', 'group_id']],
        ];
    }

    /**
     * Test key
     *
     * @dataProvider provideKeys
     * @param string $class
     * @param array  $key
     */
    public function testKey(string $class, array $key)
    {
        $policy = new Policy();

        $this->assertSame($key, $policy->key($class));
    }

    /**
     * Test without key
     */
    public function testWithoutKey()
    {
        $this->expectException(RuntimeException::class);

        $policy = new Policy();
        $policy->key(NoEntity::class);
    }

    public function testDecoratedRepository()
    {
        $dispatcher      = $this->createMock(EventDispatcherInterface::class);
        $policy          = new Policy($dispatcher);
        $connection      = $this->connection();
        $repositoryStack = $policy->repository(TimeStamps::class, $connection);
        $properties      = $policy->properties(TimeStamps::class);

        $repository = new Repository($connection, TimeStamps::class, 'time_stamps', ['id'], $properties);

        $deletedAtProperty = $this->propertyDateTime(TimeStamps::class, 'deletedAt');
        $softDelete        = new SoftDelete($repository, 'deleted_at', $deletedAtProperty);

        $createdAtProperty = $this->propertyDateTime(TimeStamps::class, 'createdAt');
        $createdAt         = new CreatedAt($softDelete, 'created_at', $createdAtProperty);

        $updatedAtProperty = $this->propertyDateTime(TimeStamps::class, 'updatedAt');
        $updatedAt         = new UpdatedAt($createdAt, 'updated_at', $updatedAtProperty);

        $eventDispatcher   = new EventDispatcher($updatedAt, $dispatcher, TimeStamps::EVENTS);

        $this->assertEquals($eventDispatcher, $repositoryStack);
    }
}
