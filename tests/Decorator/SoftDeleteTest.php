<?php

/** @noinspection SqlResolve */

namespace Neat\Object\Test\Decorator;

use DateTime;
use Generator;
use Neat\Database\QueryInterface;
use Neat\Database\SQLQuery;
use Neat\Object\Collection;
use Neat\Object\Decorator\SoftDelete;
use Neat\Object\Query;
use Neat\Object\RepositoryInterface;
use Neat\Object\Test\Helper\Assertions;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SoftDeleteTest extends TestCase
{
    use Assertions;
    use Factory;

    /**
     * Test iterate
     */
    public function testIterate(): void
    {
        $repository = $this->repository();
        $column     = 'deleted_at';
        $property   = 'deletedDate';
        $softDelete = $this->softDelete($repository, $column, User::class, $property);

        $query = new Query($this->connection(), $repository);
        $query->select('*')->from('user');
        $expectedQuery = clone $query;
        $expectedQuery->where([$column => null]);
        $repository->expects($this->once())
            ->method('query')
            ->willReturn($query);
        $repository->expects($this->once())
            ->method('iterate')
            ->with($this->equalTo($expectedQuery))
            ->willReturnCallback(
                function () {
                    yield from ['test'];
                }
            );
        $generator = $softDelete->iterate();
        $this->assertInstanceOf(Generator::class, $generator);
        foreach ($generator as $item) {
            $this->assertSame('test', $item);
        }
    }

    public function testIterateSQLQuery(): void
    {
        $repository = $this->repository();
        $column     = 'deleted_at';
        $property   = 'deletedDate';
        $softDelete = $this->softDelete($repository, $column, User::class, $property);

        $query = new SQLQuery($this->connection(), "SELECT * FROM `user`");
        $repository->expects($this->once())
            ->method('iterate')
            ->with($this->equalTo($query))
            ->willReturnCallback(
                function () {
                    yield from ['test'];
                }
            );
        $generator = $softDelete->iterate($query);
        $this->assertInstanceOf(Generator::class, $generator);
        foreach ($generator as $item) {
            $this->assertSame('test', $item);
        }
    }

    public function provideQueryBuilderData(): array
    {
        return [
            ['all', null, $this->query(['where']), [(object) []]],
            ['all', ['param' => 1], $this->query(['where']), [(object) []]],
            ['one', null, $this->query(['where']), (object) []],
            ['one', ['param' => 1], $this->query(['where']), (object) []],
            ['collection', null, $this->query(['where']), new Collection([(object) []])],
            ['collection', ['param' => 1], $this->query(['where']), new Collection([(object) []])],
        ];
    }

    /**
     * @dataProvider provideQueryBuilderData
     * @param string           $method
     * @param                  $queryParameters
     * @param Query|MockObject $query
     * @param                  $result
     */
    public function testQueryBuilders(string $method, $queryParameters, Query $query, $result): void
    {
        $repository = $this->repository();
        $column     = 'deleted_at';
        $property   = 'deletedDate';
        $softDelete = $this->softDelete($repository, $column, User::class, $property);

        $expectedQuery = $this->query([]);
        $query->expects($this->once())->method('where')->with([$column => null])->willReturn($expectedQuery);
        $queryMethod = $repository->expects($this->once())->method('query')->willReturn($query);
        if ($queryParameters !== null) {
            $queryMethod->with(['param' => 1]);
        }
        $repository->expects($this->once())
            ->method($method)
            ->with($this->equalTo($expectedQuery))
            ->willReturn($result);

        $this->assertSame($result, $softDelete->{$method}($queryParameters));
    }

    public function providerSQLData(): array
    {
        $query = new SQLQuery($this->connection(), "SELECT * FROM `user`");

        return [
            ['all', $query, [(object) []]],
            ['one', $query, (object) []],
            ['collection', $query, new Collection([(object) []])],
        ];
    }

    /**
     * @dataProvider providerSQLData
     * @param string   $method
     * @param SQLQuery $query
     * @param          $result
     */
    public function testSQLQuery(string $method, SQLQuery $query, $result): void
    {
        $repository = $this->repository();
        $column     = 'deleted_at';
        $property   = 'deletedDate';
        $softDelete = $this->softDelete($repository, $column, User::class, $property);

        $repository->expects($this->once())
            ->method($method)
            ->with($this->equalTo($query))
            ->willReturn($result);

        $this->assertSame($result, $softDelete->{$method}($query));
    }

    private function softDelete(
        RepositoryInterface $repository,
        string $column,
        string $class,
        string $property
    ): SoftDelete {
        return new SoftDelete($repository, $column, $this->propertyDateTime($class, $property));
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
     * Test delete
     */
    public function testDelete(): void
    {
        $repository = $this->repository();
        $softDelete = new SoftDelete(
            $repository,
            'deleted_date',
            $this->propertyDateTime(User::class, 'deletedDate')
        );
        $date       = null;
        $repository->expects($this->once())
            ->method('store')
            ->with(
                $this->callback(
                    function (User $user) use (&$date) {
                        $date = $user->deletedDate;
                        if (is_null($user->deletedDate)) {
                            return false;
                        }
                        if (!$user->deletedDate instanceof DateTime) {
                            return false;
                        }

                        return true;
                    }
                )
            );
        $user = new User();
        $softDelete->delete($user);
        $this->assertSame($date, $user->deletedDate);
        $softDelete->delete($user);
        $this->assertSame($date, $user->deletedDate);
    }

    /**
     * @return RepositoryInterface|MockObject
     */
    private function repository()
    {
        return $this->getMockForAbstractClass(RepositoryInterface::class);
    }
}
