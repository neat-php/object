<?php

namespace Neat\Object\Test;

use Generator;
use Neat\Object\Collection;
use Neat\Object\Query;
use Neat\Object\Repository;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    use Factory;

    /**
     * Get mocked repository
     *
     * @param string[] $methods
     * @return MockObject|Repository
     */
    public function getMockedRepository($methods)
    {
        return $this->getMockBuilder(Repository::class)
            ->setMethods($methods)
            ->setConstructorArgs([$this->connection(), User::class, 'user', ['id'], []])
            ->getMock();
    }

    /**
     * Provide facade expectations
     *
     * @return array
     */
    public function providerFacadeExpectations()
    {
        return [
            ['one', new User],
            ['all', [new User]],
            ['collection', new Collection([new User])],
        ];
    }

    /**
     * Test facade methods
     *
     * @dataProvider providerFacadeExpectations
     * @param string $method
     * @param mixed  $result
     */
    public function testFacades(string $method, $result)
    {
        $repository = $this->getMockedRepository([$method]);
        $repository->expects($this->once())
            ->method($method)
            ->willReturn($result);

        $query = new Query($this->connection(), $repository);
        $query->select('*')->from('user');
        $response = $query->{$method}();
        $this->assertSame($result, $response);
    }

    /**
     * Test iterating object query results
     */
    public function testIterate()
    {
        $generator = function () {
            $data = [new User];

            foreach ($data as $item) {
                yield $item;
            }
        };

        $repository = $this->getMockedRepository(['iterate']);
        $repository->expects($this->once())
            ->method('iterate')
            ->willReturnCallback($generator);

        $query = new Query($this->connection(), $repository);
        $query->select('*')->from('user');

        $response = $query->iterate();

        $this->assertInstanceOf(Generator::class, $response);
        $this->assertEquals($generator(), $response);
    }
}
