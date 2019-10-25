<?php

namespace Neat\Object\Test;

use Neat\Object\Cache;
use PHPUnit\Framework\TestCase;
use stdClass;

class CacheTest extends TestCase
{
    /**
     * Test has
     */
    public function testHas()
    {
        $cache = new Cache();

        $cache->set('test1', new stdClass());
        $this->assertFalse($cache->has('test'));
        $this->assertTrue($cache->has('test1'));
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $factory = function () {
            return new stdClass();
        };

        $cache = new Cache();

        $object1 = $cache->get('get', $factory);
        // Should reference the same object
        $this->assertSame($object1, $cache->get('get', $factory));
        $this->assertEquals($factory(), $object1);
        $object2 = $cache->get('get2', $factory);
        $this->assertNotSame($object1, $object2);
        $this->assertEquals($object1, $object2);
    }

    /**
     * Test all
     */
    public function testAll()
    {
        $objects = [
            'test1' => new stdClass(),
            'test2' => new stdClass(),
        ];

        $cache = new Cache();

        $this->assertSame([], $cache->all());
        foreach ($objects as $key => $object) {
            $cache->set($key, $object);
        }
        $this->assertSame($objects, $cache->all());
        $object3 = new stdClass();
        $cache->set('test3', $object3);
        $objects['test3'] = $object3;
        $this->assertSame($objects, $cache->all());
    }

    /**
     * Test set
     */
    public function testSet()
    {
        $factory = function () {
            return new stdClass();
        };

        $cache = new Cache();

        $object = new stdClass();
        $this->assertSame($object, $cache->set('set', $object));
        $this->assertSame($object, $cache->get('set', $factory));
    }
}
