<?php

namespace Neat\Object\Test\Decorator;

use DateTime;
use Generator;
use Neat\Object\Collection;
use Neat\Object\Decorator\SoftDelete;
use Neat\Object\Property;
use Neat\Object\Query;
use Neat\Object\Repository;
use Neat\Object\RepositoryInterface;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\SQLHelper;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class SoftDeleteTest extends TestCase
{
    use SQLHelper;

    /**
     * Test iterate
     */
    public function testIterate()
    {
        $repository = $this->repository(['query', 'iterate']);
        $softDelete = new SoftDelete(
            $repository,
            'deletedDate',
            new Property(new ReflectionProperty(User::class, 'deletedDate'))
        );
        $query      = new Query((new Factory)->connection(), $repository);
        $query->select('*')->from('user');
        $expectedQuery = clone $query;
        $expectedQuery->where(['deletedDate' => null]);
        $repository->expects($this->once())
            ->method('query')
            ->willReturn($query);
        $repository->expects($this->once())
            ->method('iterate')
            ->with($this->equalTo($expectedQuery))
            ->willReturnCallback(function () {
                yield from ['test'];
            });
        $generator = $softDelete->iterate();
        $this->assertInstanceOf(Generator::class, $generator);
        foreach ($generator as $item) {
            $this->assertSame('test', $item);
        }

    }

    /**
     * Test all
     */
    public function testAll()
    {
        $repository = $this->repository(['query', 'all']);
        $softDelete = new SoftDelete(
            $repository,
            'deletedDate',
            new Property(new ReflectionProperty(User::class, 'deletedDate'))
        );
        $query      = new Query((new Factory)->connection(), $repository);
        $query->select('*')->from('user');
        $expectedQuery = clone $query;
        $expectedQuery->where(['deletedDate' => null]);
        $repository->expects($this->once())
            ->method('query')
            ->willReturn($query);
        $repository->expects($this->once())
            ->method('all')
            ->with($this->equalTo($expectedQuery))
            ->willReturn(['test']);
        $this->assertSame(['test'], $softDelete->all());
    }

    /**
     * Test collection
     */
    public function testCollection()
    {
        $repository = $this->repository(['query', 'collection']);
        $softDelete = new SoftDelete(
            $repository,
            'deletedDate',
            new Property(new ReflectionProperty(User::class, 'deletedDate'))
        );
        $query      = new Query((new Factory)->connection(), $repository);
        $query->select('*')->from('user');
        $expectedQuery = clone $query;
        $expectedQuery->where(['deletedDate' => null]);
        $repository->expects($this->once())
            ->method('query')
            ->willReturn($query);
        $collection = new Collection(['test']);
        $repository->expects($this->once())
            ->method('collection')
            ->with($this->equalTo($expectedQuery))
            ->willReturn($collection);
        $this->assertSame($collection, $softDelete->collection());
    }

    /**
     * Test delete
     */
    public function testDelete()
    {
        $repository = $this->repository(['store']);
        $softDelete = new SoftDelete(
            $repository,
            'deletedDate',
            new Property(new ReflectionProperty(User::class, 'deletedDate'))
        );
        $date       = null;
        $repository->expects($this->once())
            ->method('store')
            ->with($this->callback(function (User $user) use (&$date) {
                $date = $user->deletedDate;
                if (is_null($user->deletedDate)) {
                    return false;
                }
                if (!$user->deletedDate instanceof DateTime) {
                    return false;
                }

                return true;
            }));
        $user = new User;
        $softDelete->delete($user);
        $this->assertSame($date, $user->deletedDate);
        $softDelete->delete($user);
        $this->assertSame($date, $user->deletedDate);
    }

    /**
     * @param array $methods
     * @return RepositoryInterface|MockObject
     */
    private function repository(array $methods)
    {
        return $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }
}
