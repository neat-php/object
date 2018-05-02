<?php

namespace Neat\Object;

abstract class Model
{
    use EntityTrait;
    use ArrayConversion;

    /**
     * Returns the default remote identifier by convention tableName_id
     *
     * @return string
     */
    public static function getRemoteIdentifier()
    {
        return static::getTableName() . '_id';
    }

    /**
     * Get the name of the called class without the namespace
     * @return string
     */
    public static function getTableName()
    {
        $path = explode('\\', static::class);

        return strtolower(array_pop($path));
    }
}
