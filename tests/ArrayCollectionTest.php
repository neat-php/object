<?php

namespace Neat\Object\Test;

use Neat\Object\Collection;
use Neat\Object\Test\Helper\User;
use Neat\Object\Test\Helper\UserGroup;
use PHPUnit\Framework\TestCase;

class ArrayCollectionTest extends TestCase
{
    private $array;

    /**
     * @var Collection
     */
    private $arrayCollection;

    public function setUp()
    {
        $this->array           = [
            'jdoe'    => ['firstName' => 'John', 'middleName' => null, 'lastName' => 'Doe'],
            'janedoe' => ['firstName' => 'Jane', 'middleName' => null, 'lastName' => 'Doe'],
            'bthecow' => ['firstName' => 'Bob', 'middleName' => 'the', 'lastName' => 'Cow'],
        ];
        $this->arrayCollection = new Collection($this->array);
    }

    public function testCount()
    {
        $this->assertSame(3, $this->arrayCollection->count());
    }

    public function testFirst()
    {
        $data = reset($this->array);
        $this->assertSame($data, $this->arrayCollection->first());
        // Assert that it didn't change
        $this->assertSame($data, $this->arrayCollection->first());

    }

    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->arrayCollection['jdoe']));
        $this->assertFalse(isset($this->arrayCollection['notExistingKey']));
    }

    public function testOffsetUnset()
    {
        unset($this->arrayCollection['jdoe']);
        $this->assertFalse(isset($this->arrayCollection['jdoe']));
    }

    public function testMap()
    {
        $firstNames = $this->firstNames();
        $this->assertSame($firstNames, $this->arrayCollection->map(function ($data) {
            return $data['firstName'];
        }));
        $firstNameCollection = new Collection($firstNames);
        $this->assertEquals($firstNameCollection, $this->arrayCollection->map(function ($data) {
            return $data['firstName'];
        }, Collection::class));
    }

    public function testOffsetGet()
    {
        $data = reset($this->array);
        $this->assertSame($data, $this->arrayCollection['jdoe']);
//        $this->assertNull($this->arrayCollection['notExistingKey']);
    }

    public function testPush()
    {
        $arrayCollection = new Collection([]);
        $arrayCollection->push("test");
        $this->assertSame('test', $arrayCollection->first());
        $this->assertCount(1, $arrayCollection);
    }

    public function testColumn()
    {
        $firstNames = array_values($this->firstNames());
        $this->assertSame($firstNames, $this->arrayCollection->column('firstName'));
        $this->assertEquals(new Collection($firstNames),
            $this->arrayCollection->column('firstName', Collection::class));

    }

    public function testOffsetSet()
    {
        $this->arrayCollection['test'] = 'test';
        $this->assertSame('test', $this->arrayCollection['test']);
        $this->assertCount(4, $this->arrayCollection);
    }

    public function testFilter()
    {
        $expected = [
            'jdoe'    => $this->array['jdoe'],
            'janedoe' => $this->array['janedoe'],
        ];

        $this->assertEquals(new Collection($expected), $this->arrayCollection->filter(function ($data): bool {
            return !$data['middleName'];
        }));
    }

    public function testGetIterator()
    {
        $arrayCollection = new Collection(['foo' => 'bar',]);
        foreach ($arrayCollection as $key => $value) {
            $this->assertSame('foo', $key);
            $this->assertSame('bar', $value);
            break;
        }
    }

    public function testTypedArray()
    {
        $user            = new User();
        $arrayCollection = new Collection([$user]);
        $this->assertSame(User::class, $this->getPrivateProperty($arrayCollection, 'type'));
        $this->assertSame($user, $arrayCollection->first());

        $this->expectException(\TypeError::class);
        $arrayCollection->push(new UserGroup);
    }

    public function testTypeDefinedArray()
    {
        $user            = new User();
        $arrayCollection = new Collection([$user], User::class);
        $this->assertSame(User::class, $this->getPrivateProperty($arrayCollection, 'type'));

        $this->expectException(\TypeError::class);
        $arrayCollection->push(new UserGroup);
    }

    private function firstNames()
    {
        return array_map(function ($data) {
            return $data['firstName'];
        }, $this->array);
    }

    private function getPrivateProperty($class, $property)
    {
        $reflectionClass = new \ReflectionClass($class);

        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);

        if (is_object($class)) {
            return $reflectionProperty->getValue($class);
        }

        return $reflectionProperty->getValue();
    }
}
