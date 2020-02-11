<?php

namespace Neat\Object\Test\Repository;

use DateTime;
use Generator;
use Neat\Database\QueryInterface;
use Neat\Database\SQLQuery;
use Neat\Object\Collection;
use Neat\Object\Query;
use Neat\Object\Repository;
use Neat\Object\Repository\SoftDelete;
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
    public function testIterate()
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

    public function testIterateSQLQuery()
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

    public function provideQueryBuilderData()
    {
        return [
            ['all', null, $this->query(['where']), ['test']],
            ['all', ['param' => 1], $this->query(['where']), ['test']],
            ['one', null, $this->query(['where']), 'test'],
            ['one', ['param' => 1], $this->query(['where']), 'test'],
            ['collection', null, $this->query(['where']), new Collection(['test'])],
            ['collection', ['param' => 1], $this->query(['where']), new Collection(['test'])],
        ];
    }

    /**
     * @dataProvider provideQueryBuilderData
     * @param string           $method
     * @param                  $queryParameters
     * @param Query|MockObject $query
     * @param                  $result
     */
    public function testQueryBuilders(string $method, $queryParameters, Query $query, $result)
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

    public function providerSQLData()
    {
        $query = new SQLQuery($this->connection(), "SELECT * FROM `user`");

        return [
            ['all', $query, ['test']],
            ['one', $query, 'test'],
            ['collection', $query, new Collection(['test'])],
        ];
    }

    /**
     * @dataProvider providerSQLData
     * @param string   $method
     * @param SQLQuery $query
     * @param          $result
     */
    public function testSQLQuery(string $method, SQLQuery $query, $result)
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
        Repository $repository,
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
        return $this->getMockBuilder(Query::class)
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test delete
     */
    public function testDelete()
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
     * @return Repository|MockObject
     */
    private function repository()
    {
        return $this->getMockForAbstractClass(Repository::class);
    }
}
