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
     * @param array|null $row
     * @return static|null
     */
    public static function createFromArray($row)
    {
        if (is_null($row)) {
            return null;
        }

        return new static();
    }

    /**
     * Finds an model by it's primary key, pass an array in case of an composed key
     *
     * @param integer|array $id
     *
     * @return static|null
     */
    public static function findById($id)
    {
        $query = static::query();
        if (is_array($id)) {
            $query->where($id);
        } else {
            $query->where([static::getIdentifier() => $id]);
        }

        return static::createFromArray($query->query()->row());
    }

    /**
     * @param array|string $where
     * @param null|string $orderBy
     * @return static[]
     */
    public static function findAll($where, $orderBy = null)
    {
        return array_map(
            [static::class, 'createFromArray'],
            static::query()->where($where)->orderBy($orderBy)->query()->rows()
        );
    }

    /**
     * Returns a query for the called class
     * Selects all columns from the table of the class
     *
     * @TODO define a strategy for a table name prefix
     *
     * @return Query
     */
    public static function query()
    {
        return (new Query(static::$connection))->select('*')->from(static::getTableName());
    }

    /**
     * Returns the default primary key, overwrite this function to define a custom primary key or an composed key
     * For a composed key return an array of strings for example:
     *   [
     *     'user_id',
     *     'group_id',
     *   ]
     *
     * @return int|array
     */
    public static function getIdentifier()
    {
        return 'id';
    }

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

    /**
     * A model requires a connection, for now we use the static $connection property to define what connection to use
     *
     * @param Connection $connection
     */
    public static function setConnection(Connection $connection)
    {
        static::$connection = $connection;
    }
}
