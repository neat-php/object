<?php

namespace Neat\Object\Test;

use Neat\Object\Collection;
use Neat\Object\Test\Helper\GroupUser;
use Neat\Object\Test\Helper\User;
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
    public function testAll(): void
    {
        $this->assertSame($this->items(), $this->collection()->all());
    }

    /**
     * Test count
     */
    public function testCount(): void
    {
        $this->assertSame(3, $this->collection()->count());
    }

    /**
     * Test first
     */
    public function testFirst(): void
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
    public function testLast(): void
    {
        $items = $this->items();
        $last  = end($items);

        $this->assertSame($last, $this->collection()->last());
        $this->assertSame($last, $this->collection()->last());
    }

    public function testFirstLastEmptyCollection(): void
    {
        $collection = new Collection([]);
        $this->assertNull($collection->first());
        $this->assertNull($collection->last());
    }

    /**
     * Test array offset exists
     */
    public function testOffsetExists(): void
    {
        $collection = $this->collection();

        $this->assertTrue(isset($collection['jdoe']));
        $this->assertFalse(isset($collection['notExistingKey']));
    }

    /**
     * Test array offset unset
     */
    public function testOffsetUnset(): void
    {
        $collection = $this->collection();

        unset($collection['jdoe']);
        $this->assertFalse(isset($collection['jdoe']));
    }

    /**
     * Test array offset get
     */
    public function testOffsetGet(): void
    {
        $items = $this->items();
        $first = reset($items);
        $this->assertSame($first, $this->collection()['jdoe']);
    }

    /**
     * Test array offset set
     */
    public function testOffsetSet(): void
    {
        $collection = $this->collection();

        $collection['test'] = 'test';
        $this->assertSame('test', $collection['test']);
        $this->assertCount(4, $collection);
    }

    /**
     * Test push
     */
    public function testPush(): void
    {
        $arrayCollection = new Collection([]);
        $arrayCollection->push("test");
        $this->assertSame('test', $arrayCollection->first());
        $this->assertCount(1, $arrayCollection);
    }

    /**
     * Test column
     */
    public function testColumn(): void
    {
        $expected = new Collection(array_values($this->firstNames()));
        $this->assertEquals($expected, $this->collection()->column('firstName'));
    }

    /**
     * Test map
     */
    public function testMap(): void
    {
        $firstNameCollection = new Collection($this->firstNames());
        $this->assertEquals(
            $firstNameCollection,
            $this->collection()->map(
                function ($data) {
                    return $data['firstName'];
                }
            )
        );
    }

    /**
     * Test filter
     */
    public function testFilter(): void
    {
        $initial    = [null, false, true, 0, 1];
        $collection = new Collection($initial);
        $expected   = new Collection([2 => true, 4 => 1]);
        $filtered   = $collection->filter();

        $this->assertEquals($expected, $filtered);
        $this->assertEquals($initial, $collection->all());
        $this->assertNotSame($collection, $filtered);
    }

    /**
     * Test callback filter
     */
    public function testCallbackFilter(): void
    {
        $expected = [
            'jdoe'    => $this->items()['jdoe'],
            'janedoe' => $this->items()['janedoe'],
        ];
        $filtered = $this->collection()->filter(
            function ($data): bool {
                return !$data['middleName'];
            }
        );

        $this->assertEquals(new Collection($expected), $filtered);
    }

    public function testSort(): void
    {
        $initial    = [5, 1, 3, 2, 4];
        $collection = new Collection($initial);
        $expected   = new Collection([1, 2, 3, 4, 5]);
        $sorted     = $collection->sort();

        $this->assertEquals($expected, $sorted);
        $this->assertEquals($initial, $collection->all());
        $this->assertNotSame($collection, $sorted);
    }

    public function testCallbackSort(): void
    {
        $initial    = [5, 1, 3, 2, 4];
        $collection = new Collection($initial);
        $expected   = new Collection([5, 4, 3, 2, 1]);
        $sorted     = $collection->sort(
            function (int $a, int $b): int {
                return $b <=> $a;
            }
        );

        $this->assertEquals($expected, $sorted);
        $this->assertEquals($initial, $collection->all());
        $this->assertNotSame($collection, $sorted);
    }

    /**
     * Test get iterator
     */
    public function testGetIterator(): void
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
    public function testUntypedCollection(): void
    {
        $user       = new User();
        $collection = new Collection([$user]);
        $this->assertSame($user, $collection->first());

        $collection->push(new GroupUser());
    }

    /**
     * Test json serialize
     */
    public function testJsonSerialize(): void
    {
        $this->assertEquals(json_encode($this->items()), json_encode($this->collection()));
    }

    /**
     * Get first names
     *
     * @return array
     */
    private function firstNames(): array
    {
        return array_map(
            function ($data) {
                return $data['firstName'];
            },
            $this->items()
        );
    }
}
