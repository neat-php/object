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

    public static function getKey(): array
    {
        return ['user_id', 'group_id'];
    }

    public static function getTableName(): string
    {
        return 'user_group';
    }
}
