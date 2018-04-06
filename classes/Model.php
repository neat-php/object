<?php

namespace Neat\Object;

use Neat\Database\Connection;
use Neat\Database\Query;

abstract class Model
{
    /**
     * @var Connection
     */
    protected static $connection;

    /**
     * Finds an model by it's primary key, pass an array in case of an composed key
     *
     * @param integer|array $id
     *
     * @return static|null
     */
    public static function findById($id)
    {
        static::query()
            ->where(['id' => $id]);

        return new static();
    }

    public static function query()
    {
        return (new Query(static::$connection))->select('*')->from(static::getTableName());
    }

    public static function getTableName()
    {
        return strtolower(array_pop(explode('\\', static::class)));
    }
}
