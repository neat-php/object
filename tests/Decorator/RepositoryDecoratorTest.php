<?php

namespace Neat\Object\Test\Decorator;

use Neat\Object\Collection;
use Neat\Object\Query;
use Neat\Object\RepositoryDecorator;
use Neat\Object\RepositoryInterface;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\RepositoryDecoratorMock;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RepositoryDecoratorTest extends TestCase
{
    use Factory;

    /** @var RepositoryInterface|MockObject */
    private $repository;

    /** @var RepositoryDecorator */
    private $decorator;

    private function decorator(): RepositoryDecoratorMock
    {
        $this->repository = $this->getMockForAbstractClass(RepositoryInterface::class);
        $this->decorator  = new RepositoryDecoratorMock($this->repository);

        return $this->decorator;
    }

    public function provideMethodData()
    {
        $connection = $this->connection();

//        [$method, $arguments, $return];
        $user      = new User();
        $queryData = ['name' => 'test'];
        $arrayData = ['id' => 1];

        return [
            ['has', [1], false],
            ['has', [2], true],
            ['get', [1], null],
            ['get', [2], $user],
            ['select', [], new Query($connection, $this->repository(User::class))],
            ['select', ['alias'], new Query($connection, $this->repository(User::class))],
            ['query', [], new Query($connection, $this->repository(User::class))],
            ['query', [$queryData], new Query($connection, $this->repository(User::class))],
            ['one', [], null],
            ['one', [], null],
            ['all', [], []],
            ['all', [$queryData], [$user]],
            ['collection', [], new Collection([])],
            ['collection', [$queryData], new Collection([])],
            ['iterate', [], new \ArrayIterator()],
            ['iterate', [$queryData], new \ArrayIterator()],
            ['store', [$user], null, true],
            ['insert', [$arrayData], 1],
            ['update', [1, $arrayData], 1],
            ['load', [$user], $user],
            ['delete', [$user], 1],
            ['delete', [$user], false],
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
        $decorator = $this->decorator();
        $expects   = $this->repository->expects($this->once())->method($method);
        if ($arguments) {
            $expects->with(...$arguments);
        }
        if (!$void) {
            $expects->willReturn($returnValue);
        }
        $this->assertSame($returnValue, call_user_func_array([$decorator, $method], $arguments));
    }
}
