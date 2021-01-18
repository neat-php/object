<?php

namespace Neat\Object\Test\Decorator;

use Neat\Database\QueryInterface;
use Neat\Object\Decorator\OrderBy;
use Neat\Object\Query;
use Neat\Object\RepositoryInterface;
use Neat\Object\Test\Helper\Assertions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderByTest extends TestCase
{
    use Assertions;

    public function testQuery()
    {
        $repository = $this->repository();
        $orderBy    = new OrderBy($repository, 'name');
        $unordered  = $this->query(['orderBy']);
        $ordered    = $this->query([]);

        $repository->expects($this->once())->method('query')->with($this->isNull())->willReturn($unordered);

        $unordered->expects($this->once())->method('orderBy')->with($this->equalTo('name'))->willReturn($ordered);

        $this->assertSame($ordered, $orderBy->query());
    }

    /**
     * @param array $methods
     * @return QueryInterface|MockObject
     */
    private function query(array $methods): QueryInterface
    {
        return $this->createPartialMock(Query::class, $methods);
    }

    /**
     * @return RepositoryInterface|MockObject
     */
    private function repository()
    {
        return $this->getMockForAbstractClass(RepositoryInterface::class);
    }
}
