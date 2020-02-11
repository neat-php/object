<?php

namespace Neat\Object\Test\Helper;

use Neat\Database\Connection;
use Neat\Object\Cache;
use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\Reference\ReferenceFactory;
use Neat\Object\RepositoryInterface;

class ReferenceFactoryMock
{
    use ReferenceFactory;

    /** @var Manager */
    private $manager;

    /**
     * Constructor
     *
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager    = $manager;
        $this->references = new Cache();
    }

    public function manager(): Manager
    {
        return $this->manager;
    }

    /**
     * Get connection
     *
     * @return Connection
     */
    public function connection(): Connection
    {
        return $this->manager->connection();
    }

    /**
     * Get policy
     *
     * @return Policy
     */
    public function policy(): Policy
    {
        return $this->manager->policy();
    }

    /**
     * Get repository
     *
     * @param string $class
     * @return RepositoryInterface
     */
    public function repository(string $class): RepositoryInterface
    {
        return $this->manager->repository($class);
    }
}
