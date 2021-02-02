<?php

namespace Neat\Object\Test\Relations;

use Neat\Object\Query;
use Neat\Object\Relations\Many;
use Neat\Object\Relations\Reference;
use Neat\Object\Relations\Reference\RemoteKey;
use Neat\Object\RepositoryInterface;
use Neat\Object\Test\Helper\Address;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use TypeError;

class ManyTest extends TestCase
{
    use Factory;

    /**
     * Create reference
     *
     * @return RemoteKey|MockObject
     */
    public function mockedRemoteKey()
    {
        return $this->createPartialMock(RemoteKey::class, ['load', 'store', 'getRemoteKeyValue']);
    }

    /**
     * Create many relation
     *
     * @param Reference|null $reference
     * @return Many
     */
    public function many(Reference $reference = null): Many
    {
        $user     = new User();
        $user->id = 1;

        return new Many($reference ?? $this->mockedRemoteKey(), $user);
    }

    /**
     * Test get
     */
    public function testAll()
    {
        $address         = new Address();
        $address->id     = 1;
        $address->userId = 1;

        $reference = $this->mockedRemoteKey();
        $reference
            ->expects($this->once())
            ->method('load')
            ->willReturn([$address]);

        $many = $this->many($reference);

        $this->assertSame([$address], $many->all());
        $this->assertSame([$address], $many->get());
    }

    /**
     * Test getEmpty
     */
    public function testAllEmpty()
    {
        $reference = $this->mockedRemoteKey();
        $reference
            ->expects($this->once())
            ->method('load')
            ->willReturn([]);

        $many = $this->many($reference);

        $this->assertSame([], $many->all());
        $this->assertSame([], $many->get());
    }

    /**
     * Test set
     */
    public function testSet()
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

        $many = $this->many($reference);
        $many->set([$address]);
        $many->store();

        $many->set([]);
        $many->store();

        $this->expectException(TypeError::class);
        $many->set(null);
    }

    /**
     * Test add
     */
    public function testAdd()
    {
        $address1         = new Address();
        $address1->id     = 1;
        $address1->userId = 1;

        $address2         = new Address();
        $address2->id     = 2;
        $address2->userId = 1;

        $user     = new User();
        $user->id = 1;

        $reference = $this->mockedRemoteKey();
        $reference
            ->expects($this->once())
            ->method('load')
            ->willReturn([$address1]);
        $reference
            ->expects($this->exactly(2))
            ->method('getRemoteKeyValue')
            ->willReturnOnConsecutiveCalls(['id' => $address1->id], ['id' => $address2->id]);
        $reference
            ->expects($this->once())
            ->method('store')
            ->with($this->equalTo($user), $this->equalTo([$address1, $address2]));

        $many = $this->many($reference);
        $many->add($address2);
        $many->store();
    }

    public function testHas()
    {
        $address1         = new Address();
        $address1->id     = 1;
        $address1->userId = 1;

        $address2         = new Address();
        $address2->id     = 2;
        $address2->userId = 1;

        $reference = $this->mockedRemoteKey();
        $reference
            ->expects($this->once())
            ->method('load')
            ->willReturn([$address1]);
        $reference
            ->expects($this->exactly(4))
            ->method('getRemoteKeyValue')
            ->withConsecutive([$address1], [$address1], [$address2], [$address1])
            ->willReturnOnConsecutiveCalls(
                ['id' => $address1->id],
                ['id' => $address1->id],
                ['id' => $address2->id],
                ['id' => $address1->id]
            );

        $many = $this->many($reference);

        $this->assertTrue($many->has($address1));
        $this->assertFalse($many->has($address2));
    }

    /**
     * Test add multiple
     */
    public function testAddMultiple()
    {
        $address1         = new Address();
        $address1->userId = 1;

        $address2         = new Address();
        $address2->userId = 2;

        $user     = new User();
        $user->id = 1;

        $reference = $this->mockedRemoteKey();
        $reference
            ->expects($this->once())
            ->method('load')
            ->willReturn([$address1]);
        $reference
            ->expects($this->once())
            ->method('getRemoteKeyValue')
            ->willReturn(['id' => $address2->id]);
        $reference
            ->expects($this->once())
            ->method('store')
            ->with($this->equalTo($user), $this->equalTo([$address1, $address2]));

        $many = $this->many($reference);
        $many->add($address2);
        $many->store();
    }

    /**
     * Test remove
     */
    public function testRemove()
    {
        $address1         = new Address();
        $address1->id     = 1;
        $address1->userId = 1;

        $address2         = new Address();
        $address2->id     = 2;
        $address2->userId = 1;

        $user     = new User();
        $user->id = 1;

        $reference = $this->mockedRemoteKey();
        $reference
            ->expects($this->once())
            ->method('load')
            ->willReturn([$address1, $address2]);
        $reference
            ->expects($this->exactly(2))
            ->method('getRemoteKeyValue')
            ->with($address1)
            ->willReturn($address1->id);
        $reference
            ->expects($this->once())
            ->method('store')
            ->with($this->equalTo($user), $this->equalTo([$address2]));

        $many = $this->many($reference);
        $this->assertSame($many, $many->remove($address1));
        $many->store();
    }

    /**
     * Test remove
     */
    public function testRemoveNonRelatedArticle()
    {
        $address1         = new Address();
        $address1->id     = 1;
        $address1->userId = 1;

        $user     = new User();
        $user->id = 1;

        $reference = $this->mockedRemoteKey();
        $reference
            ->expects($this->once())
            ->method('load')
            ->willReturn([]);
        $reference
            ->expects($this->once())
            ->method('store')
            ->with($user, []);

        $many = $this->many($reference);
        $this->assertSame($many, $many->remove($address1));
        $many->store();
    }

    public function testSelect()
    {
        $reference = $this->getMockForAbstractClass(Reference::class);
        $user      = new User();
        $query     = new Query($this->connection(), $this->getMockForAbstractClass(RepositoryInterface::class));
        $reference->expects($this->once())
            ->method('select')
            ->with($user)
            ->willReturn($query);
        $relation = new Many($reference, $user);
        $this->assertSame($query, $relation->select());
    }
}
