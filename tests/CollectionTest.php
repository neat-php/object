<?php

namespace Neat\Object\Test;

use Neat\Object\Collection;
use Neat\Object\Test\Helper\User;
use Neat\Object\Test\Helper\GroupUser;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var array
     */
    private $array;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * Setup before each test method
     */
    public function setUp()
    {
        $this->array      = [
            'jdoe'    => ['firstName' => 'John', 'middleName' => null, 'lastName' => 'Doe'],
            'janedoe' => ['firstName' => 'Jane', 'middleName' => null, 'lastName' => 'Doe'],
            'bthecow' => ['firstName' => 'Bob', 'middleName' => 'the', 'lastName' => 'Cow'],
        ];
        $this->collection = new Collection($this->array);
    }

    /**
     * Test all
     */
    public function testAll()
    {
        $this->assertSame($this->array, $this->collection->all());
    }

    /**
     * Test count
     */
    public function testCount()
    {
        $this->assertSame(3, $this->collection->count());
    }

    /**
     * Test first
     */
    public function testFirst()
    {
        $data = reset($this->array);
        $this->assertSame($data, $this->collection->first());
        // Assert that it didn't change
        $this->assertSame($data, $this->collection->first());
    }

    public function testLast()
    {
        $data = end($this->array);
        $this->assertSame($data, $this->collection->last());
        $this->assertSame($data, $this->collection->last());
    }

    /**
     * Test array offset exists
     */
    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->collection['jdoe']));
        $this->assertFalse(isset($this->collection['notExistingKey']));
    }

    /**
     * Test array offset unset
     */
    public function testOffsetUnset()
    {
        unset($this->collection['jdoe']);
        $this->assertFalse(isset($this->collection['jdoe']));
    }

    /**
     * Test array offset get
     */
    public function testOffsetGet()
    {
        $data = reset($this->array);
        $this->assertSame($data, $this->collection['jdoe']);
//        $this->assertNull($this->arrayCollection['notExistingKey']);
    }

    /**
     * Test array offset set
     */
    public function testOffsetSet()
    {
        $this->collection['test'] = 'test';
        $this->assertSame('test', $this->collection['test']);
        $this->assertCount(4, $this->collection);
    }

    /**
     * Test push
     */
    public function testPush()
    {
        $arrayCollection = new Collection([]);
        $arrayCollection->push("test");
        $this->assertSame('test', $arrayCollection->first());
        $this->assertCount(1, $arrayCollection);
    }

    /**
     * Test column
     */
    public function testColumn()
    {
        $expected = new Collection(array_values($this->firstNames()));
        $this->assertEquals($expected, $this->collection->column('firstName'));
    }

    /**
     * Test map
     */
    public function testMap()
    {
        $firstNameCollection = new Collection($this->firstNames());
        $this->assertEquals($firstNameCollection, $this->collection->map(function ($data) {
            return $data['firstName'];
        }));
    }

    /**
     * Test filter
     */
    public function testFilter()
    {
        $collection = new Collection([null, false, true, 0, 1]);
        $expected   = new Collection([2 => true, 4 => 1]);
        $filtered   = $collection->filter();

        $this->assertEquals($expected, $filtered);
    }

    /**
     * Test callback filter
     */
    public function testCallbackFilter()
    {
        $expected = [
            'jdoe'    => $this->array['jdoe'],
            'janedoe' => $this->array['janedoe'],
        ];
        $filtered = $this->collection->filter(function ($data): bool {
            return !$data['middleName'];
        });

        $this->assertEquals(new Collection($expected), $filtered);
    }

    /**
     * Test get iterator
     */
    public function testGetIterator()
    {
        $arrayCollection = new Collection(['foo' => 'bar',]);
        foreach ($arrayCollection as $key => $value) {
            $this->assertSame('foo', $key);
            $this->assertSame('bar', $value);
            break;
        }
    }

    /**
     * Test untyped collection
     */
    public function testUntypedCollection()
    {
        $user       = new User();
        $collection = new Collection([$user]);
        $this->assertSame($user, $collection->first());

        $collection->push(new GroupUser);
    }

    /**
     * Test json serialize
     */
    public function testJsonSerialize()
    {
        $this->assertEquals(json_encode($this->array), json_encode($this->collection));
    }

    /**
     * Get first names
     *
     * @return array
     */
    private function firstNames()
    {
        return array_map(function ($data) {
            return $data['firstName'];
        }, $this->array);
    }
}
