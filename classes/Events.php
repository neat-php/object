<?php

namespace Neat\Object;

use Neat\Object\Decorator\EventDispatcher;

trait Events
{
    abstract public static function repository(): RepositoryInterface;

    /**
     * Trigger a named event for this entity
     *
     * @param string $event
     */
    public function trigger(string $event)
    {
        $this::repository()->layer(EventDispatcher::class)->trigger($event, $this);
    }
}
