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

    /**
     * Setup before each test method
     */
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

    /**
     * Test get
     */
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

    /**
     * Test getNull
     */
    public function testGetNull()
    {
        $this->reference->expects($this->once())
            ->method('load')
            ->willReturn([]);

        $get = $this->relation->get();
        $this->assertNull($get);
    }

    /**
     * Test set
     */
    public function testSet()
    {
        $address         = new Address;
        $address->id     = 1;
        $address->userId = 1;

        $this->reference->expects($this->at(0))
            ->method('store')
            ->with($this->equalTo($this->user), $this->equalTo([$address]));
        $this->reference->expects($this->at(1))
            ->method('store')
            ->with($this->equalTo($this->user), $this->equalTo([]));

        $this->relation->set($address);
        $this->relation->store();

        $this->relation->set(null);
        $this->relation->store();
    }
}
