<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\ArrayConversion;
use Neat\Object\EntityTrait;
use Neat\Object\Repository;

class Weirdo
{
    use EntityTrait;
    use ArrayConversion;

    public static function repository()
    {
        return new Repository(static::getEntityManager(), static::class, static::getTableName(), static::getIdentifier());
    }

    public static function getTableName()
    {
        return 'user_weirdo';
    }

    public static function getIdentifier()
    {
        return 'key';
    }
}
