<?php

namespace Neat\Object;

use RuntimeException;
use Neat\Database\Connection;

class Manager
{
    use ReferenceFactory;

    /**
     * @var self[]
     */
    private static $instances = [];

    /**
     * @var callable[]
     */
    private static $factories = [];

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Policy
     */
    private $policy;

    /**
     * @var Cache
     */
    private $repositories;

    /**
     * Manager constructor
     *
     * @param Connection $connection
     * @param Policy     $policy
     */
    public function __construct(Connection $connection, Policy $policy)
    {
        $this->connection   = $connection;
        $this->policy       = $policy;
        $this->repositories = new Cache;
        $this->references   = new Cache;
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
     * @return RepositoryInterface
     */
    public function repository(string $class): RepositoryInterface
    {
        /** @var RepositoryInterface $repository */
        $repository = $this->repositories->get($class, function () use ($class) {
            return $this->policy->repository($class, $this->connection);
        });

        return $repository;
    }

    /**
     * Get manager instance
     *
     * @param string $manager
     * @return Manager
     */
    public static function get(string $manager = 'default'): Manager
    {
        return self::$instances[$manager]
            ?? self::getFactory($manager);
    }

    /**
     * Create manager using factory
     *
     * @param string $manager
     * @return Manager
     */
    private static function getFactory(string $manager = 'default'): Manager
    {
        if (!isset(self::$factories[$manager])) {
            throw new RuntimeException('Object manager not set: ' . $manager);
        }

        self::$instances[$manager] = (self::$factories[$manager])();
        self::$factories[$manager] = null;

        return self::$instances[$manager];
    }

    /**
     * Set manager instance
     *
     * @param Manager $instance
     * @param string  $manager
     */
    public static function set(Manager $instance, string $manager = 'default')
    {
        self::$instances[$manager] = $instance;
        self::$factories[$manager] = null;
    }

    /**
     * Set manager factory
     *
     * @param callable $factory
     * @param string   $manager
     */
    public static function setFactory(callable $factory, string $manager = 'default')
    {
        self::$instances[$manager] = null;
        self::$factories[$manager] = $factory;
    }

    /**
     * Is manager set?
     *
     * @param string $manager
     * @return bool
     */
    public static function isset(string $manager = 'default'): bool
    {
        return isset(self::$instances[$manager])
            || isset(self::$factories[$manager]);
    }

    /**
     * Unset manager
     *
     * @param string $manager
     */
    public static function unset(string $manager = 'default')
    {
        unset(self::$instances[$manager]);
        unset(self::$factories[$manager]);
    }

    /**
     * Get manager instance
     *
     * @param string $instance
     * @return Manager
     * @deprecated Use get() instead
     */
    public static function instance(string $instance = 'default')
    {
        return self::get($instance);
    }

    /**
     * Create manager instance
     *
     * @param Connection $connection
     * @param Policy     $policy
     * @param string     $instance
     * @return Manager
     * @deprecated Use set() instead
     */
    public static function create(Connection $connection, Policy $policy = null, string $instance = 'default')
    {
        if (!isset(self::$instances[$instance])) {
            self::set(new self($connection, $policy ?: new Policy), $instance);
        }

        return self::$instances[$instance];
    }
}
