<?php

namespace Neat\Object\Test\Relations;

use Neat\Object\Manager;
use Neat\Object\Relations\Many;
use Neat\Object\Relations\One;
use Neat\Object\Relations\Reference\JunctionTableBuilder;
use Neat\Object\Relations\Reference\LocalKeyBuilder;
use Neat\Object\Relations\Reference\RemoteKeyBuilder;
use Neat\Object\Relations\Relation;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\CallableMock;
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
    public function testHasOne(): void
    {
        Manager::set($this->manager());

        $user     = new User();
        $relation = $user->hasOne(Address::class);
        $this->assertInstanceOf(Relation::class, $relation);

        $expected = new One(Manager::get()->remoteKey('testHasOne', User::class, Address::class), $user);
        $this->assertEquals($expected, $relation);
        $this->assertSame($relation, $user->hasOne(Address::class));

        $configure = $this->createPartialMock(CallableMock::class, ['__invoke']);
        $configure->expects($this->once())->method('__invoke')->with($this->isInstanceOf(RemoteKeyBuilder::class));
        $user->hasOne(Address::class, 'hasOneAddress', $configure);
    }

    /**
     * Test has many
     *
     * @runInSeparateProcess enabled
     */
    public function testHasMany(): void
    {
        Manager::set($this->manager());

        $user     = new User();
        $relation = $user->hasMany(Address::class);
        $this->assertInstanceOf(Relation::class, $relation);

        $expected = new Many(Manager::get()->remoteKey('testHasMany', User::class, Address::class), $user);
        $this->assertEquals($expected, $relation);
        $this->assertSame($relation, $user->hasMany(Address::class));

        $configure = $this->createPartialMock(CallableMock::class, ['__invoke']);
        $configure->expects($this->once())->method('__invoke')->with($this->isInstanceOf(RemoteKeyBuilder::class));
        $user->hasMany(Address::class, 'hasManyAddresses', $configure);
    }

    /**
     * Test belongs to one
     *
     * @runInSeparateProcess enabled
     */
    public function testBelongsToOne(): void
    {
        Manager::set($this->manager());

        $user     = new User();
        $relation = $user->belongsToOne(Type::class);
        $this->assertInstanceOf(Relation::class, $relation);

        $expected = new One(Manager::get()->localKey('testBelongsToOne', User::class, Type::class), $user);
        $this->assertEquals($expected, $relation);
        $this->assertSame($relation, $user->belongsToOne(Type::class));

        $configure = $this->createPartialMock(CallableMock::class, ['__invoke']);
        $configure->expects($this->once())->method('__invoke')->with($this->isInstanceOf(LocalKeyBuilder::class));
        $user->belongsToOne(Type::class, 'belongsToOneType', $configure);
    }

    /**
     * Test belongs to many
     *
     * @runInSeparateProcess enabled
     */
    public function testBelongsToMany(): void
    {
        Manager::set($this->manager());

        $user     = new User();
        $relation = $user->belongsToMany(Address::class);
        $this->assertInstanceOf(Relation::class, $relation);
        $this->assertInstanceOf(Many::class, $relation);

        $expected = new Many(Manager::get()->junctionTable('testBelongsToMany', User::class, Address::class), $user);
        $this->assertEquals($expected, $relation);
        $this->assertSame($relation, $user->belongsToMany(Address::class));

        $configure = $this->createPartialMock(CallableMock::class, ['__invoke']);
        $configure->expects($this->once())->method('__invoke')->with($this->isInstanceOf(JunctionTableBuilder::class));
        $user->belongsToMany(Address::class, 'belongsToManyAddress', $configure);
    }
}
