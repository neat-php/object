<?php

namespace Neat\Object\Test\Helper;

use DateTime;
use Neat\Object\Event;

class TimeStamps extends Entity
{
    const EVENTS = [
        Event::STORING => Event\Storing::class,
    ];

    /** @var int */
    public $id;

    /** @var DateTime */
    public $createdAt;

    /** @var DateTime */
    public $updatedAt;

    /** @var DateTime */
    public $deletedAt;
}
