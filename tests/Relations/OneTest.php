<?php

namespace Neat\Object\Test\Relations;

use Neat\Object\Query;
use Neat\Object\Relations\One;
use Neat\Object\Relations\Reference;
use Neat\Object\Relations\Reference\RemoteKey;
use Neat\Object\RepositoryInterface;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OneTest extends TestCase
{
    use Factory;

    /**
     * Create reference
     *
     * @return RemoteKey|MockObject
     */
    public function mockedRemoteKey()
    {
        return $this->createPartialMock(RemoteKey::class, ['load', 'store']);
    }

    /**
     * Create one relation
     *
     * @param Reference|null $reference
     * @return One
     */
    public function one(Reference $reference = null): One
    {
        $user     = new User();
        $user->id = 1;

        return new One($reference ?? $this->mockedRemoteKey(), $user);
    }

    /**
     * Test get
     */
    public function testGet(): void
    {
        $address         = new Address();
        $address->id     = 1;
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
    public function testGetNull(): void
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
    public function testSet(): void
    {
        $address         = new Address();
        $address->id     = 1;
        $address->userId = 1;

        $user     = new User();
        $user->id = 1;

        $reference = $this->mockedRemoteKey();
        $reference
            ->expects($this->exactly(2))
            ->method('store')
            ->withConsecutive(
                [$this->equalTo($user), $this->equalTo([$address])],
                [$this->equalTo($user), $this->equalTo([])]
            );

        $one = $this->one($reference);

        $one->set($address);
        $one->store();

        $one->set(null);
        $one->store();
    }

    public function testSelect(): void
    {
        $reference = $this->getMockForAbstractClass(Reference::class);
        $user      = new User();
        $query     = new Query($this->connection(), $this->getMockForAbstractClass(RepositoryInterface::class));
        $reference->expects($this->once())
            ->method('select')
            ->with($user)
            ->willReturn($query);
        $relation = new One($reference, $user);
        $this->assertSame($query, $relation->select());
    }
}
