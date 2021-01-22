<?php

namespace Neat\Object;

use Neat\Object\Decorator\EventDispatcher;
use Neat\Object\Exception\EventNotDefinedException;

trait Events
{
    abstract public static function repository(): RepositoryInterface;

    /**
     * Trigger a named event for this entity
     *
     * @param string $event
     * @return void
     * @throws EventNotDefinedException In case the event is not defined on the entity.
     */
    public function trigger(string $event): void
    {
        $this::repository()->layer(EventDispatcher::class)->trigger($event, $this);
    }

    /**
     * Trigger a named event for this entity if the event is not defined nothing happens
     *
     * @param string $event
     * @return void
     */
    public function triggerIfExists(string $event): void
    {
        $this::repository()->layer(EventDispatcher::class)->triggerIfExists($event, $this);
    }
}
