<?php

namespace Neat\Object\Test;

use Neat\Object\Cache;
use PHPUnit\Framework\TestCase;
use stdClass;

class CacheTest extends TestCase
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * Setup before each test method
     */
    public function setUp()
    {
        $this->cache = new Cache;
    }

    /**
     * Test has
     */
    public function testHas()
    {
        $this->cache->set('test1', new stdClass);
        $this->assertFalse($this->cache->has('test'));
        $this->assertTrue($this->cache->has('test1'));
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $factory = function () {
            return new stdClass;
        };

        $object1 = $this->cache->get('get', $factory);
        // Should reference the same object
        $this->assertSame($object1, $this->cache->get('get', $factory));
        $this->assertEquals($factory(), $object1);
        $object2 = $this->cache->get('get2', $factory);
        $this->assertNotSame($object1, $object2);
        $this->assertEquals($object1, $object2);
    }

    /**
     * Test all
     */
    public function testAll()
    {
        $objects = [
            'test1' => new stdClass,
            'test2' => new stdClass,
        ];

        $this->assertSame([], $this->cache->all());
        foreach ($objects as $key => $object) {
            $this->cache->set($key, $object);
        }
        $this->assertSame($objects, $this->cache->all());
        $object3 = new stdClass;
        $this->cache->set('test3', $object3);
        $objects['test3'] = $object3;
        $this->assertSame($objects, $this->cache->all());
    }

    /**
     * Test set
     */
    public function testSet()
    {
        $factory = function () {
            return new stdClass;
        };

        $object = new stdClass;
        $this->assertSame($object, $this->cache->set('set', $object));
        $this->assertSame($object, $this->cache->get('set', $factory));
    }
}
