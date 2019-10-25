<?php

namespace Neat\Object\Test;

use Neat\Object\Collection;
use Neat\Object\Test\Helper\User;
use Neat\Object\Test\Helper\GroupUser;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    /**
     * Get items
     *
     * @return array
     */
    private function items(): array
    {
        return [
            'jdoe'    => ['firstName' => 'John', 'middleName' => null, 'lastName' => 'Doe'],
            'janedoe' => ['firstName' => 'Jane', 'middleName' => null, 'lastName' => 'Doe'],
            'bthecow' => ['firstName' => 'Bob', 'middleName' => 'the', 'lastName' => 'Cow'],
        ];
    }

    /**
     * Get collection
     *
     * @return Collection
     */
    private function collection(): Collection
    {
        return new Collection($this->items());
    }

    /**
     * Test all
     */
    public function testAll()
    {
        $this->assertSame($this->items(), $this->collection()->all());
    }

    /**
     * Test count
     */
    public function testCount()
    {
        $this->assertSame(3, $this->collection()->count());
    }

    /**
     * Test first
     */
    public function testFirst()
    {
        $items = $this->items();
        $first = reset($items);

        $this->assertSame($first, $this->collection()->first());
        // Assert that it didn't change
        $this->assertSame($first, $this->collection()->first());
    }

    /**
     * Test last
     */
    public function testLast()
    {
        $items = $this->items();
        $last  = end($items);

        $this->assertSame($last, $this->collection()->last());
        $this->assertSame($last, $this->collection()->last());
    }

    /**
     * Test array offset exists
     */
    public function testOffsetExists()
    {
        $collection = $this->collection();

        $this->assertTrue(isset($collection['jdoe']));
        $this->assertFalse(isset($collection['notExistingKey']));
    }

    /**
     * Test array offset unset
     */
    public function testOffsetUnset()
    {
        $collection = $this->collection();

        unset($collection['jdoe']);
        $this->assertFalse(isset($collection['jdoe']));
    }

    /**
     * Test array offset get
     */
    public function testOffsetGet()
    {
        $items = $this->items();
        $first = reset($items);
        $this->assertSame($first, $this->collection()['jdoe']);
    }

    /**
     * Test array offset set
     */
    public function testOffsetSet()
    {
        $collection = $this->collection();

        $collection['test'] = 'test';
        $this->assertSame('test', $collection['test']);
        $this->assertCount(4, $collection);
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
        $this->assertEquals($expected, $this->collection()->column('firstName'));
    }

    /**
     * Test map
     */
    public function testMap()
    {
        $firstNameCollection = new Collection($this->firstNames());
        $this->assertEquals($firstNameCollection, $this->collection()->map(function ($data) {
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
            'jdoe'    => $this->items()['jdoe'],
            'janedoe' => $this->items()['janedoe'],
        ];
        $filtered = $this->collection()->filter(function ($data): bool {
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
        $this->assertEquals(json_encode($this->items()), json_encode($this->collection()));
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
        }, $this->items());
    }
}
