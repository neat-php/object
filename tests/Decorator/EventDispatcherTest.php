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

    public function provideMethods()
    {
        return [
            ['load', $entity = new Events(), $entity, 'loading', 'loaded', Event\Loading::class, Event\Loaded::class],
            ['delete', new Events(), 1, 'deleting', 'deleted', Event\Deleting::class, Event\Deleted::class],
            ['store', new Events(), null, 'updating', 'updated', Event\Updating::class, Event\Updated::class],
            ['store', new Events(), null, 'creating', 'created', Event\Creating::class, Event\Created::class]
        ];
    }

    /**
     * @param string $method
     * @param object $in
     * @param mixed  $out
     * @param string $pre
     * @param string $post
     * @param string $preClass
     * @param string $postClass
     * @dataProvider provideMethods
     */
    public function testMethod($method, $in, $out, $pre, $post, $preClass, $postClass)
    {
        $log = [];

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(new $preClass($in))
            ->willReturnCallback(function () use (&$log, $pre) {
                $log[] = $pre;
            });

        $dispatcher
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(new $postClass($in))
            ->willReturnCallback(function () use (&$log, $post) {
                $log[] = $post;
            });

        $repository = $this->createMock(Repository::class);
        if ($pre =='updating') {
            $repository
                ->expects($this->at(0))
                ->method('identifier')
                ->willReturn(['id' => 1]);
            $repository
                ->expects($this->once())
                ->method('has')
                ->willReturn(true);
        }
        $repository
            ->expects($this->once())
            ->method($method)
            ->with($in)
            ->willReturnCallback(function () use (&$log, $method, $out) {
                $log[] = $method;

                return $out;
            });

        $eventDispatcher = new EventDispatcher($repository, $dispatcher, Events::EVENTS);

        $this->assertSame($out, $eventDispatcher->$method($in));
        $this->assertSame([$pre, $method, $post], $log);
    }

    public function provideEvents()
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
    public function testEvent(string $class)
    {
        $user  = new User();
        $event = new $class($user);

        $this->assertSame($user, $event->entity());
    }
}
