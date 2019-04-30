<?php

namespace Neat\Object\Test\Helper;

use Neat\Database\Connection;
use Neat\Object\Cache;
use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\ReferenceFactory;
use Neat\Object\RepositoryInterface;

class ReferenceFactoryMock
{
    use ReferenceFactory;

    /**
     * @var Manager
     */
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager    = $manager;
        $this->references = new Cache;
    }

    public function connection(): Connection
    {
        return $this->manager->connection();
    }

    public function policy(): Policy
    {
        return $this->manager->policy();
    }

    public function repository(string $class): RepositoryInterface
    {
        return $this->manager->repository($class);
    }
}
