<?php

namespace Neat\Object;

use Neat\Database\Connection;

/**
 * Class Manager
 * @package Neat\Object
 */
class Manager
{
    /**
     * @var self[]
     */
    private static $instances = [];

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Repository[]
     */
    private $repositories = [];

    /**
     * Manager constructor.
     * @param Connection $connection
     */
    private function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param string|Entity $entity
     * @return Repository
     */
    public function repository(string $entity)
    {
        if (!isset($this->repositories[$entity])) {
            $this->repositories[$entity] = new Repository($this->connection, $entity, $entity::getTableName(),
                $entity::getKey());
        }

        return $this->repositories[$entity];
    }

    /**
     * @param string $instance
     * @return Manager
     */
    public static function instance(string $instance = 'default')
    {
        return self::$instances[$instance];
    }

    /**
     * @param Connection $connection
     * @param string $instance
     * @return Manager
     */
    public static function create(Connection $connection, string $instance = 'default')
    {
        if (!isset(self::$instances[$instance])) {
            self::$instances[$instance] = new self($connection);
        }

        return self::$instances[$instance];
    }
}
