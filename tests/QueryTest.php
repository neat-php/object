<?php

namespace Neat\Object\Test;

use Neat\Database\Connection;
use Neat\Object\Collection;
use Neat\Object\Query;
use Neat\Object\Repository;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    /**
     * @var Connection
     */
    private $connection;

    public function setUp()
    {
        $factory          = new Factory();
        $this->connection = $factory->connection();
    }

    public function mock($methods)
    {
        return $this->getMockBuilder(Repository::class)
            ->setMethods($methods)
            ->setConstructorArgs([$this->connection, User::class, 'user', ['id'], []])
            ->getMock();
    }

    public function dataProvider()
    {
        return [
            ['one', new User],
            ['all', [new User]],
            ['collection', new Collection([new User])],
        ];
    }

    /**
     * @dataProvider dataProvider
     * @param $method
     * @param $result
     */
    public function testFacades(string $method, $result)
    {
        /** @var Repository|MockObject $repository */
        $repository = $this->mock([$method]);
        $repository->expects($this->once())
            ->method($method)
            ->willReturn($result);

        $query = new Query($this->connection, $repository);
        $query->select('*')->from('user');
        $response = $query->{$method}();
        $this->assertSame($result, $response);
    }

    public function testIterate()
    {
        $generator = function () {
            $data = [new User];

            foreach ($data as $item) {
                yield $item;
            }
        };

        /** @var Repository|MockObject $repository */
        $repository = $this->mock(['iterate']);
        $repository->expects($this->once())
            ->method('iterate')
            ->willReturnCallback($generator);

        $query = new Query($this->connection, $repository);
        $query->select('*')->from('user');
        $response = $query->iterate();

        $this->assertInstanceOf(\Generator::class, $response);
        $this->assertEquals($generator(), $response);
    }
}
