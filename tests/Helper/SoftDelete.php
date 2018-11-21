<?php

/** @noinspection PhpMissingDocCommentInspection */

namespace Neat\Object\Test\Helper;

use DateTime;
use Neat\Object\Identifier;

class SoftDelete extends Entity
{
    use Identifier;

    /**
     * @var DateTime
     */
    public $deletedDate;

}
