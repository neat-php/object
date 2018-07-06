<?php

namespace Neat\Object\Test;

use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Group;
use Neat\Object\Test\Helper\NoEntity;
use Neat\Object\Test\Helper\User;
use Neat\Object\Test\Helper\UserGroup;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RuntimeException;

class PolicyTest extends TestCase
{
    /**
     * @var Policy
     */
    private $policy;

    /**
     * Setup before each test method
     */
    protected function setUp()
    {
        $this->policy = new Policy;
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Create property
     *
     * @param string $name
     * @return Property
     */
    public function createProperty($name)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $reflection = new ReflectionProperty(User::class, $name);
        $property   = new Property($reflection);

        return $property;
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
        $property = $this->createProperty($name);

        $this->assertSame($column, $this->policy->column($property));
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
        $this->assertEquals($foreignKey, $this->policy->foreignKey($class));
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
            [Address::class, 'my_address_table'],
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
        $this->assertSame($table, $this->policy->table($entity));
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
        $this->assertEquals($junctionTable, $this->policy->junctionTable($owner, $owned));
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

        $this->assertSame($skip, $this->policy->skip($property));
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
            [UserGroup::class, ['user_id', 'group_id']],
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
        $this->assertSame($key, $this->policy->key($class));
    }

    /**
     * Test without key
     */
    public function testWithoutKey()
    {
        $this->expectException(RuntimeException::class);
        $this->policy->key(NoEntity::class);
    }

    /**
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
     * @dataProvider provideForeignKeys
     * @param string $class
     * @param string $foreignKey
     */
    public function testForeignKey(string $class, string $foreignKey)
    {
        $this->assertEquals($foreignKey, $this->policy->foreignKey($class));
    }

    /**
     * @return array
     */
    public function provideJunctionTables()
    {
        return [
            [User::class, Group::class, 'user_group'],
            [Group::class, User::class, 'group_user'],
        ];
    }

    /**
     * @dataProvider provideJunctionTables
     * @param string $owner
     * @param string $owned
     * @param string $junctionTable
     */
    public function testJunctionTable($owner, $owned, $junctionTable)
    {
        $this->assertEquals($junctionTable, $this->policy->junctionTable($owner, $owned));
    }
}
