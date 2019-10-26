<?php

namespace Neat\Object\Test\Relations;

use Neat\Object\Relations\One;
use Neat\Object\Relations\Reference;
use Neat\Object\Relations\Reference\RemoteKey;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OneTest extends TestCase
{
    /**
     * Create reference
     *
     * @return RemoteKey|MockObject
     */
    public function mockedRemoteKey()
    {
        return $this->getMockBuilder(RemoteKey::class)
            ->disableOriginalConstructor()
            ->setMethods(['load', 'store'])
            ->getMock();
    }

    /**
     * Create one relation
     *
     * @param Reference|null $reference
     * @return One
     */
    public function one(Reference $reference = null): One
    {
        $user = new User;
        $user->id = 1;

        return new One($reference ?? $this->mockedRemoteKey(), $user);
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $address = new Address;
        $address->id = 1;
        $address->userId = 1;

        $reference = $this->mockedRemoteKey();
        $reference
            ->expects($this->once())
            ->method('load')
            ->willReturn([$address]);

        $one = $this->one($reference);

        $this->assertSame($address, $one->get());
    }

    /**
     * Test getNull
     */
    public function testGetNull()
    {
        $reference = $this->mockedRemoteKey();
        $reference
            ->expects($this->once())
            ->method('load')
            ->willReturn([]);

        $one = $this->one($reference);

        $this->assertNull($one->get());
    }

    /**
     * Test set
     */
    public function testSet()
    {
        $address = new Address;
        $address->id = 1;
        $address->userId = 1;

        $user = new User;
        $user->id = 1;

        $reference = $this->mockedRemoteKey();
        $reference
            ->expects($this->at(0))
            ->method('store')
            ->with($this->equalTo($user), $this->equalTo([$address]));
        $reference
            ->expects($this->at(1))
            ->method('store')
            ->with($this->equalTo($user), $this->equalTo([]));

        $one = $this->one($reference);

        $one->set($address);
        $one->store();

        $one->set(null);
        $one->store();
    }
}
