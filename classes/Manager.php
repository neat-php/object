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
     * @var Policy
     */
    private $policy;

    /**
     * Manager constructor.
     * @param Connection $connection
     * @param Policy $policy
     */
    private function __construct(Connection $connection, Policy $policy)
    {
        $this->connection = $connection;
        $this->policy     = $policy;
    }

    /**
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * @param string $entity
     * @return Repository
     */
    public function repository(string $entity)
    {
        if (!isset($this->repositories[$entity])) {
            $this->repositories[$entity] = $this->createRepository($entity);
        }

        return $this->repositories[$entity];
    }

    /**
     * @param string $entity
     * @return Repository
     * @throws \ReflectionException
     */
    private function createRepository(string $entity)
    {
        $connection = $this->connection;
        $table      = $this->policy->table($entity);
        $properties = Property::list($entity, $this->policy);
        $key        = $this->policy->key($properties);

        return new Repository($connection, $entity, $table, $key, $properties);
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
     * @param Policy|null $policy
     * @param string $instance
     * @return Manager
     */
    public static function create(Connection $connection, Policy $policy = null, string $instance = 'default')
    {
        if (!isset(self::$instances[$instance])) {
            self::$instances[$instance] = new self($connection, $policy ?: new Policy);
        }

        return self::$instances[$instance];
    }
}
