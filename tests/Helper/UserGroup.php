<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Entity;

class UserGroup extends Entity
{
    /**
     * @key
     * @var int
     */
    public $userId;

    /**
     * @key
     * @var int
     */
    public $groupId;
}
