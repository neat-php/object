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

    public function provideMethods(): iterable
    {
        $entity = new Events();

        yield ['load', $entity, $entity, null, [new Event\Loading($entity), new Event\Loaded($entity)]];
        $entity = new Events();
        yield ['delete', $entity, 1, null, [new Event\Deleting($entity), new Event\Deleted($entity)]];
        $entity = new Events();
        $events = [new Event\Storing($entity), new Event\Updating($entity), new Event\Stored($entity), new Event\Updated($entity)];
        yield ['store', $entity, null, 1, $events,];
        $entity = new Events();
        $events = [new Event\Storing($entity), new Event\Creating($entity), new Event\Stored($entity), new Event\Created($entity)];
        yield ['store', $entity, null, null, $events,];
    }

    /**
     * @param string       $method
     * @param object       $in
     * @param mixed        $out
     * @param int|null     $id
     * @param array<Event> $events
     * @dataProvider provideMethods
     */
    public function testMethod(string $method, object $in, $out, ?int $id, array $events)
    {
        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher
            ->expects($this->exactly(count($events)))
            ->method('dispatch')
            ->withConsecutive(...array_chunk($events, 1));

        $repository = $this->createMock(Repository::class);
        if ($id) {
            $repository
                ->expects($this->once())
                ->method('identifier')
                ->willReturn(['id' => $id]);
            $repository
                ->expects($this->once())
                ->method('has')
                ->willReturn(true);
        }
        $repository
            ->expects($this->once())
            ->method($method)
            ->with($in)
            ->willReturn($out);

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
    public function testEvent(string $class)
    {
        $user  = new User();
        $event = new $class($user);

        $this->assertSame($user, $event->entity());
    }
}
