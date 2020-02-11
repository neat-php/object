<?php

namespace Neat\Object\Test\Relation;

use Neat\Object\Manager;
use Neat\Object\Relation;
use Neat\Object\Relation\Many;
use Neat\Object\Relation\One;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\Type;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;

class RelationsTest extends TestCase
{
    use Factory;

    /**
     * Test has one
     *
     * @runInSeparateProcess enabled
     */
    public function testHasOne()
    {
        Manager::set($this->manager());

        $user     = new User();
        $relation = $user->hasOne(Address::class);
        $this->assertInstanceOf(Relation::class, $relation);

        $expected = new One(Manager::get()->remoteKey(User::class, Address::class), $user);
        $this->assertEquals($expected, $relation);
        $this->assertSame($relation, $user->hasOne(Address::class));
    }

    /**
     * Test has many
     *
     * @runInSeparateProcess enabled
     */
    public function testHasMany()
    {
        Manager::set($this->manager());

        $user     = new User();
        $relation = $user->hasMany(Address::class);
        $this->assertInstanceOf(Relation::class, $relation);

        $expected = new Many(Manager::get()->remoteKey(User::class, Address::class), $user);
        $this->assertEquals($expected, $relation);
        $this->assertSame($relation, $user->hasMany(Address::class));
    }

    /**
     * Test belongs to one
     *
     * @runInSeparateProcess enabled
     */
    public function testBelongsToOne()
    {
        Manager::set($this->manager());

        $user     = new User();
        $relation = $user->belongsToOne(Type::class);
        $this->assertInstanceOf(Relation::class, $relation);

        $expected = new One(Manager::get()->localKey(User::class, Type::class), $user);
        $this->assertEquals($expected, $relation);
        $this->assertSame($relation, $user->belongsToOne(Type::class));
    }

    /**
     * Test belongs to many
     *
     * @runInSeparateProcess enabled
     */
    public function testBelongsToMany()
    {
        Manager::set($this->manager());

        $user     = new User();
        $relation = $user->belongsToMany(Address::class);
        $this->assertInstanceOf(Relation::class, $relation);
        $this->assertInstanceOf(Many::class, $relation);

        $expected = new Many(Manager::get()->junctionTable(User::class, Address::class), $user);
        $this->assertEquals($expected, $relation);
        $this->assertSame($relation, $user->belongsToMany(Address::class));
    }
}
