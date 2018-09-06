<?php

namespace Neat\Object\Test\Relations;

use Neat\Object\Manager;
use Neat\Object\Relations\HasOne;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;

class HasOneTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        Manager::create((new Factory())->connection());
    }

    public function test__construct()
    {
        $user = User::get(1);

        $hasOne = new HasOne($user, Manager::instance()->policy(), Manager::instance()->repository(Address::class));
        $this->assertInstanceOf(HasOne::class, $hasOne);
    }

    public function testGet()
    {
        $user = User::get(1);
        $hasOne = new HasOne($user, Manager::instance()->policy(), Manager::instance()->repository(Address::class));
        $group = $hasOne->get();

        $this->assertInstanceOf(Address::class, $group);
    }

    public function testSet()
    {
        $user = User::get(2);
        $address = new Address;

        $hasOne = new HasOne($user, Manager::instance()->policy(), Manager::instance()->repository(Address::class));
        $hasOne->set($address);

        $this->assertInstanceOf(Address::class, $hasOne->get());
        $this->assertEquals($user->id, $address->userId);
        Manager::instance()->repository(get_class($address))->store($address);

    }


}
