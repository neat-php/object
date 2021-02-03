<?php

namespace Neat\Object\Test\Decorator;

use Neat\Object\Decorator\EventDispatcher;
use Neat\Object\Event;
use Neat\Object\Repository;
use Neat\Object\Test\Helper\Events;
use Neat\Object\Test\Helper\Factory;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventDispatcherTest extends TestCase
{
    use Factory;

    public function provideMethods(): array
    {
        return [
            ['load', $entity = new Events(), $entity, Event\Loading::class, Event\Loaded::class],
            ['delete', new Events(), 1, Event\Deleting::class, Event\Deleted::class],
            ['store', new Events(), null, Event\Updating::class, Event\Updated::class],
            ['store', new Events(), null, Event\Creating::class, Event\Created::class],
        ];
    }

    /**
     * @param string $method
     * @param object $in
     * @param mixed  $out
     * @param string $preClass
     * @param string $postClass
     * @dataProvider provideMethods
     */
    public function testMethod(string $method, object $in, $out, string $preClass, string $postClass): void
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive([new $preClass($in)], [new $postClass($in)]);

        $repository = $this->createMock(Repository::class);
        if ($preClass === Event\Updating::class) {
            $repository
                ->expects($this->once())
                ->method('identifier')
                ->willReturn(['id' => 1]);
            $repository
                ->expects($this->once())
                ->method('has')
                ->willReturn(true);
        }
        $expects = $repository
            ->expects($this->once())
            ->method($method)
            ->with($in);
        if ($out !== null) {
            $expects->willReturn($out);
        }

        $eventDispatcher = new EventDispatcher($repository, $dispatcher, Events::EVENTS);

        $this->assertSame($out, $eventDispatcher->$method($in));
    }

    public function provideEvents(): array
    {
        return [
            [Event\Loading::class],
            [Event\Loaded::class],
            [Event\Storing::class],
            [Event\Stored::class],
            [Event\Deleting::class],
            [Event\Deleted::class],
            [Event\Updating::class],
            [Event\Updated::class],
            [Event\Creating::class],
            [Event\Created::class],
        ];
    }

    /**
     * @param string $class
     * @dataProvider provideEvents
     */
    public function testEvent(string $class): void
    {
        $user  = new User();
        $event = new $class($user);

        $this->assertSame($user, $event->entity());
    }
}
