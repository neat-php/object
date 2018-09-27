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
     * @var Reference|MockObject
     */
    private $reference;

    /**
     * @var User
     */
    private $user;

    /**
     * @var One
     */
    private $relation;

    public function setUp()
    {
        $this->reference = $this->getMockBuilder(RemoteKey::class)
            ->disableOriginalConstructor()
            ->setMethods(['load', 'store'])
            ->getMock();
        $this->user      = new User;
        $this->user->id  = 1;
        $this->relation  = new One($this->reference, $this->user);
    }

    public function testGet()
    {
        $address         = new Address;
        $address->id     = 1;
        $address->userId = 1;
        $this->reference->expects($this->once())
            ->method('load')
            ->willReturn([$address]);

        $get = $this->relation->get();
        $this->assertSame($address, $get);
    }

    public function testGetNull()
    {
        $this->reference->expects($this->once())
            ->method('load')
            ->willReturn([]);

        $get = $this->relation->get();
        $this->assertNull($get);
    }

    public function testSet()
    {
        $address         = new Address;
        $address->id     = 1;
        $address->userId = 1;

        $this->reference->expects($this->once())
            ->method('store')
            ->with($this->equalTo($this->user), $this->equalTo([$address]));

        $this->relation->set($address);
        $this->relation->store();
    }
}
