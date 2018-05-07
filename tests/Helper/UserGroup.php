<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Entity;

class UserGroup extends Entity
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $groupId;

    public static function getIdentifier()
    {
        return ['user_id', 'group_id'];
    }

    public static function getTableName()
    {
        return 'user_group';
    }
}
