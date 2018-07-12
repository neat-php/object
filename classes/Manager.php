<?php

namespace Neat\Object;

use Neat\Database\Connection;

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
     * Manager constructor
     *
     * @param Connection $connection
     * @param Policy     $policy
     */
    private function __construct(Connection $connection, Policy $policy)
    {
        $this->connection = $connection;
        $this->policy     = $policy;
    }

    /**
     * Get connection
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * Get or create repository
     *
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

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Create repository
     *
     * @param string $entity
     * @return Repository
     */
    private function createRepository(string $entity)
    {
        $properties = $this->policy->properties($entity);
        $table      = $this->policy->table($entity);
        $key        = $this->policy->key($properties);

        return new Repository($this->connection, $entity, $table, $key, $properties);
    }

    /**
     * Get manager instance
     *
     * @param string $instance
     * @return Manager
     */
    public static function instance(string $instance = 'default')
    {
        return self::$instances[$instance];
    }

    /**
     * Create manager instance
     *
     * @param Connection $connection
     * @param Policy     $policy
     * @param string     $instance
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
