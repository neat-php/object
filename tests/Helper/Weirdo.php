<?php

namespace Neat\Object\Test\Helper;

use Neat\Object\EntityTrait;

class Weirdo
{
    use EntityTrait;

    public static function getTableName()
    {
        return 'user_weirdo';
    }

    public static function getKey(): array
    {
        return ['key'];
    }
}
