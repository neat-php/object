<?php

namespace Neat\Object\Test;

use Neat\Object\Collection;
use Neat\Object\Test\Helper\User;
use Neat\Object\Test\Helper\UserGroup;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    private $array;

    /**
     * @var Collection
     */
    private $collection;

    public function setUp()
    {
        $this->array      = [
            'jdoe'    => ['firstName' => 'John', 'middleName' => null, 'lastName' => 'Doe'],
            'janedoe' => ['firstName' => 'Jane', 'middleName' => null, 'lastName' => 'Doe'],
            'bthecow' => ['firstName' => 'Bob', 'middleName' => 'the', 'lastName' => 'Cow'],
        ];
        $this->collection = new Collection($this->array);
    }

    public function testCount()
    {
        $this->assertSame(3, $this->collection->count());
    }

    public function testFirst()
    {
        $data = reset($this->array);
        $this->assertSame($data, $this->collection->first());
        // Assert that it didn't change
        $this->assertSame($data, $this->collection->first());

    }

    public function testOffsetExists()
    {
        $this->assertTrue(isset($this->collection['jdoe']));
        $this->assertFalse(isset($this->collection['notExistingKey']));
    }

    public function testOffsetUnset()
    {
        unset($this->collection['jdoe']);
        $this->assertFalse(isset($this->collection['jdoe']));
    }

    public function testOffsetGet()
    {
        $data = reset($this->array);
        $this->assertSame($data, $this->collection['jdoe']);
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
        $expected = new Collection(array_values($this->firstNames()));
        $this->assertEquals($expected, $this->collection->column('firstName'));
    }

    public function testOffsetSet()
    {
        $this->collection['test'] = 'test';
        $this->assertSame('test', $this->collection['test']);
        $this->assertCount(4, $this->collection);
    }

    public function testMap()
    {
        $firstNameCollection = new Collection($this->firstNames());
        $this->assertEquals($firstNameCollection, $this->collection->map(function ($data) {
            return $data['firstName'];
        }));
    }

    public function testFilter()
    {
        $collection = new Collection([null, false, true, 0, 1]);
        $expected   = new Collection([2 => true, 4 => 1]);
        $filtered   = $collection->filter();

        $this->assertEquals($expected, $filtered);
    }

    public function testTypedFilter()
    {
        $collection = new Collection([], User::class);
        $filtered   = $collection->filter();

        $this->assertEquals(User::class, $filtered->type());
    }

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
        $this->assertNull($filtered->type());
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

    public function testUntypedCollection()
    {
        $user       = new User();
        $collection = new Collection([$user]);
        $this->assertNull($collection->type());
        $this->assertSame($user, $collection->first());

        $collection->push(new UserGroup);
    }

    public function testTypedCollection()
    {
        $user       = new User();
        $collection = new Collection([$user], User::class);
        $this->assertSame(User::class, $collection->type());

        $this->expectException(\TypeError::class);
        $collection->push(new UserGroup);
    }

    public function testJsonSerialize()
    {
        $this->assertEquals(json_encode($this->array), json_encode($this->collection));
    }

    private function firstNames()
    {
        return array_map(function ($data) {
            return $data['firstName'];
        }, $this->array);
    }
}
