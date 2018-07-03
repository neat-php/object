<?php

namespace Neat\Object\Test;

use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Test\Helper\NoEntity;
use Neat\Object\Test\Helper\User;
use Neat\Object\Test\Helper\UserGroup;
use PHPUnit\Framework\TestCase;

class PolicyTest extends TestCase
{
    /**
     * @var Policy
     */
    private $policy;

    protected function setUp()
    {
        $this->policy = new Policy;
    }

    /**
     * Create property
     *
     * @param string $name
     * @return Property
     * @throws \ReflectionException
     */
    public function createProperty($name)
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $reflection = new \ReflectionProperty(User::class, $name);
        $property   = new Property($reflection);

        return $property;
    }

    /**
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
     * @dataProvider provideTables
     * @param string $entity
     * @param string $table
     */
    public function testTable(string $entity, string $table)
    {
        $this->assertSame($table, $this->policy->table($entity));
    }

    /**
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
     * @dataProvider provideSkips
     * @param string $name
     * @param bool $skip
     */
    public function testSkip(string $name, bool $skip)
    {
        $property = $this->createProperty($name);

        $this->assertSame($skip, $this->policy->skip($property));
    }

    /**
     * @return array
     */
    public function provideKeys()
    {
        return [
            [User::class, ['id']],
            [UserGroup::class, ['user_id', 'group_id']]
        ];
    }

    /**
     * @dataProvider provideKeys
     * @param string $entity
     * @param array $key
     */
    public function testKey(string $entity, array $key)
    {
        $this->assertSame($key, $this->policy->key(Property::list($entity, $this->policy)));
    }

    /**
     * @throws \ReflectionException
     */
    public function testKeyFailure()
    {
        $this->expectException(\RuntimeException::class);
        $this->policy->key(Property::list(NoEntity::class, $this->policy));
    }
}
