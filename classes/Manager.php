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
     * @var Policy
     */
    private $policy;

    /**
     * @var Repository[]
     */
    private $repositories = [];

    /**
     * Manager constructor
     *
     * @param Connection $connection
     * @param Policy     $policy
     */
    public function __construct(Connection $connection, Policy $policy)
    {
        $this->connection = $connection;
        $this->policy     = $policy;
    }

    /**
     * Get connection
     *
     * @return Connection
     */
    public function connection(): Connection
    {
        return $this->connection;
    }

    /**
     * Get policy
     *
     * @return Policy
     */
    public function policy(): Policy
    {
        return $this->policy;
    }

    /**
     * Get or create repository
     *
     * @param string $class
     * @return Repository
     */
    public function repository(string $class): Repository
    {
        return $this->repositories[$class]
            ?? $this->repositories[$class] = $this->policy->repository($class, $this->connection);
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
