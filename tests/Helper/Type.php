<?php

namespace Neat\Object\Test\Helper;

class Type
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @param array $array
     * @return Type
     */
    public static function createFromArray(array $array): Type
    {
        $type = new Type();
        $type->id   = (int) $array['id'];
        $type->name = (string) $array['name'];

        return $type;
    }
}
