<?php

namespace Neat\Object\Test\Relations;

use Neat\Object\Manager;
use Neat\Object\Relations\Many;
use Neat\Object\Relations\One;
use Neat\Object\Relations\Relation;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\Type;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;

class RelationsTest extends TestCase
{
    /**
     * Setup before class
     */
    public static function setUpBeforeClass()
    {
        (new Factory)->manager();
    }

    /**
     * Test has one
     */
    public function testHasOne()
    {
        $user     = new User;
        $relation = $user->hasOne(Address::class);
        $this->assertInstanceOf(Relation::class, $relation);
        $this->assertInstanceOf(One::class, $relation);

        $this->assertAttributeSame($user, 'local', $relation);
        $this->assertAttributeSame(Manager::instance()->remoteKey(User::class, Address::class), 'reference', $relation);
        $this->assertSame($relation, $user->hasOne(Address::class));
    }

    /**
     * Test has many
     */
    public function testHasMany()
    {
        $user     = new User;
        $relation = $user->hasMany(Address::class);
        $this->assertInstanceOf(Relation::class, $relation);
        $this->assertInstanceOf(Many::class, $relation);

        $this->assertAttributeSame($user, 'local', $relation);
        $this->assertAttributeSame(Manager::instance()->remoteKey(User::class, Address::class), 'reference', $relation);
        $this->assertSame($relation, $user->hasMany(Address::class));
    }

    /**
     * Test belongs to one
     */
    public function testBelongsToOne()
    {
        $user     = new User;
        $relation = $user->belongsToOne(Type::class);
        $this->assertInstanceOf(Relation::class, $relation);
        $this->assertInstanceOf(One::class, $relation);

        $this->assertAttributeSame($user, 'local', $relation);
        $this->assertAttributeSame(Manager::instance()->localKey(User::class, Type::class), 'reference', $relation);
        $this->assertSame($relation, $user->belongsToOne(Type::class));
    }

    /**
     * Test belongs to many
     */
    public function testBelongsToMany()
    {
        $user     = new User;
        $relation = $user->belongsToMany(Address::class);
        $this->assertInstanceOf(Relation::class, $relation);
        $this->assertInstanceOf(Many::class, $relation);

        $this->assertAttributeSame($user, 'local', $relation);
        $this->assertAttributeSame(Manager::instance()->junctionTable(
            User::class,
            Address::class
        ), 'reference', $relation);
        $this->assertSame($relation, $user->belongsToMany(Address::class));
    }
}
