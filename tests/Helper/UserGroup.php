<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\Entity;
use Neat\Object\Repository;

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

    public static function repository(): Repository
    {
        return new Repository(static::getEntityManager(), static::class, static::getTableName(), static::getIdentifier());
    }

    public static function getIdentifier()
    {
        return ['user_id', 'group_id'];
    }

    public static function getTableName()
    {
        return 'user_group';
    }
}
