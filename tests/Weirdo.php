<?php

namespace Neat\Object\Test;

use Neat\Object\ArrayConversion;
use Neat\Object\EntityTrait;

class Weirdo
{
    use EntityTrait;
    use ArrayConversion;

    public static function getTableName()
    {
        return 'user_weirdo';
    }

    public static function getIdentifier()
    {
        return 'key';
    }
}
