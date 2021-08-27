<?php

namespace Neat\Object;

use Neat\Database\Connection;
use RuntimeException;

class Manager
{
    use ReferenceFactory;

    /** @var self[] */
    private static $instances = [];

    /** @var array<callable():self> */
    private static $factories = [];

    /** @var Connection */
    private $connection;

    /** @var Policy */
    private $policy;

    /** @var Cache */
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
        $this->repositories = new Cache();
        $this->references   = new Cache();
    }

    public function manager(): Manager
    {
        return $this;
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
     * @template T
     * @param class-string<T> $class
     * @return RepositoryInterface<T>
     */
    public function repository(string $class): RepositoryInterface
    {
        /** @var RepositoryInterface<T> $repository */
        $repository = $this->repositories->get(
            $class,
            function () use ($class) {
                return $this->policy->repository($class, $this->connection);
            }
        );

        return $repository;
    }

    /**
     * Get manager instance
     *
     * @param string $manager
     * @return self
     */
    public static function get(string $manager = 'default'): self
    {
        return self::$instances[$manager]
            ?? self::getFactory($manager);
    }

    /**
     * Create manager using factory
     *
     * @param string $manager
     * @return self
     */
    private static function getFactory(string $manager = 'default'): self
    {
        if (!isset(self::$factories[$manager])) {
            throw new RuntimeException('Object manager not set: ' . $manager);
        }

        self::$instances[$manager] = (self::$factories[$manager])();
        unset(self::$factories[$manager]);

        return self::$instances[$manager];
    }

    /**
     * Set manager instance
     *
     * @param self   $instance
     * @param string $manager
     * @return void
     */
    public static function set(Manager $instance, string $manager = 'default')
    {
        self::$instances[$manager] = $instance;
        unset(self::$factories[$manager]);
    }

    /**
     * Set manager factory
     *
     * @param callable():self $factory
     * @param string          $manager
     * @return void
     */
    public static function setFactory(callable $factory, string $manager = 'default')
    {
        unset(self::$instances[$manager]);
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
     * @return void
     */
    public static function unset(string $manager = 'default')
    {
        unset(self::$instances[$manager]);
        unset(self::$factories[$manager]);
    }
}
