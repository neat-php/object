<?php

namespace Neat\Object\Test\Helper;

use DateTime;

class TimeStamps extends Entity
{
    /** @var int */
    public $id;

    /** @var DateTime */
    public $createdAt;

    /** @var DateTime */
    public $updatedAt;

    /** @var DateTime */
    public $deletedAt;

}
