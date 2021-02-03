<?php

namespace Neat\Object\Test\Decorator;

use ArrayIterator;
use Neat\Object\Collection;
use Neat\Object\Exception\LayerNotFoundException;
use Neat\Object\Query;
use Neat\Object\Repository;
use Neat\Object\RepositoryInterface;
use Neat\Object\SQLQuery;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\RepositoryDecoratorMock;
use Neat\Object\Test\Helper\Type;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;

class RepositoryDecoratorTest extends TestCase
{
    use Factory;

    public function testLayerReturnsInner(): void
    {
        $repository = $this->repository(Type::class);
        $decorator  = new RepositoryDecoratorMock($repository);

        $this->assertSame($repository, $decorator->layer(Repository::class));
    }

    public function testLayerReturnsSelf(): void
    {
        $repository = $this->repository(Type::class);
        $decorator  = new RepositoryDecoratorMock($repository);

        $this->assertSame($decorator, $decorator->layer(RepositoryDecoratorMock::class));
    }

    public function testLayerNotFoundException(): void
    {
        $repository = $this->repository(Type::class);

        $this->expectException(LayerNotFoundException::class);

        $repository->layer(RepositoryDecoratorMock::class);
    }

    public function provideMethodData(): array
    {
        $connection = $this->connection();

//        [$method, $arguments, $return];
        $user      = new User();
        $queryData = ['name' => 'test'];
        $arrayData = ['id' => 1];
        $sql       = 'SELECT * FROM dual';

        return [
            ['has', [1], false],
            ['has', [2], true],
            ['get', [1], null],
            ['get', [2], $user],
            ['select', [], new Query($connection, $this->repository(User::class))],
            ['select', ['alias'], new Query($connection, $this->repository(User::class))],
            ['query', [], new Query($connection, $this->repository(User::class))],
            ['query', [$queryData], new Query($connection, $this->repository(User::class))],
            ['sql', [$sql], new SQLQuery($connection, $this->repository(User::class), $sql)],
            ['one', [], null],
            ['one', [], null],
            ['all', [], []],
            ['all', [$queryData], [$user]],
            ['collection', [], new Collection([])],
            ['collection', [$queryData], new Collection([])],
            ['iterate', [], new ArrayIterator()],
            ['iterate', [$queryData], new ArrayIterator()],
            ['store', [$user], null, true],
            ['insert', [$arrayData], 1],
            ['update', [1, $arrayData], 1],
            ['load', [$user], $user],
            ['delete', [$user], 1],
            ['delete', [$user], 0],
            ['toArray', [$user], $arrayData],
            ['fromArray', [$user, $arrayData], $user],
            ['create', [$arrayData], $user],
            ['identifier', [$user], ['id' => 1]],
        ];
    }

    /**
     * @dataProvider provideMethodData
     * @param string     $method
     * @param array      $arguments
     * @param mixed|null $returnValue
     * @param bool       $void
     */
    public function testMethods(string $method, array $arguments = [], $returnValue = null, $void = false)
    {
        $repository = $this->getMockForAbstractClass(RepositoryInterface::class);
        $decorator  = new RepositoryDecoratorMock($repository);
        $expects    = $repository->expects($this->once())->method($method);
        if ($arguments) {
            $expects->with(...$arguments);
        }
        if (!$void) {
            $expects->willReturn($returnValue);
        }
        $this->assertSame($returnValue, call_user_func_array([$decorator, $method], $arguments));
    }
}
