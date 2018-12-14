<?php

/** @noinspection PhpMissingDocCommentInspection */

namespace Neat\Object\Test\Helper;

use DateTime;
use Neat\Object\Identifier;

class TimeStamps extends Entity
{
    use Identifier;

    /**
     * @var DateTime
     */
    public $createdAt;

    /**
     * @var DateTime
     */
    public $updatedAt;

    /**
     * @var DateTime
     */
    public $deletedAt;

}
