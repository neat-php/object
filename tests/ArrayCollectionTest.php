<?php

namespace Neat\Object\Test;

use Neat\Object\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ArrayCollectionTest extends TestCase
{
    private $array;

    /**
     * @var ArrayCollection
     */
    private $arrayCollection;

    public function setUp()
    {
        $this->array           = [
            'jdoe'    => ['firstName' => 'John', 'middleName' => null, 'lastName' => 'Doe'],
            'janedoe' => ['firstName' => 'Jane', 'middleName' => null, 'lastName' => 'Doe'],
            'bthecow' => ['firstName' => 'Bob', 'middleName' => 'the', 'lastName' => 'Cow'],
        ];
        $this->arrayCollection = new ArrayCollection($this->array);
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
        $this->assertSame($this->firstNames(), $this->arrayCollection->map(function ($data) {
            return $data['firstName'];
        }));
        $firstNameCollection = new ArrayCollection($this->firstNames());
        $this->assertEquals($firstNameCollection, $this->arrayCollection->map(function ($data) {
            return $data['firstName'];
        }, ArrayCollection::class));
    }

    public function testOffsetGet()
    {
        $data = reset($this->array);
        $this->assertSame($data, $this->arrayCollection['jdoe']);
//        $this->assertNull($this->arrayCollection['notExistingKey']);
    }

//    public function testPush()
//    {
//
//    }
//
//    public function testColumn()
//    {
//
//    }
//
//    public function testOffsetSet()
//    {
//
//    }
//
//    public function testFilter()
//    {
//
//    }
//
//    public function testGetIterator()
//    {
//
//    }

    private function firstNames()
    {
        return array_map(function ($data) {
            return $data['firstName'];
        }, $this->array);
    }
}
