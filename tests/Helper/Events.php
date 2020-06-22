<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Event;

class Events
{
    public const EVENTS = [
        Event::LOADING  => Event\Loading::class,
        Event::LOADED   => Event\Loaded::class,
        Event::STORING  => Event\Storing::class,
        Event::STORED   => Event\Stored::class,
        Event::DELETING => Event\Deleting::class,
        Event::DELETED  => Event\Deleted::class,
    ];
}
