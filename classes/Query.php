<?php

namespace Neat\Object;

use Neat\Database\Connection;

/**
 * @template T of object
 */
class Query extends \Neat\Database\Query
{
    /** @use QueryRepository<T> */
    use QueryRepository;

    /**
     * Query constructor
     *
     * @param Connection             $connection
     * @param RepositoryInterface<T> $repository
     */
    public function __construct(Connection $connection, RepositoryInterface $repository)
    {
        parent::__construct($connection);

        $this->repository = $repository;
    }
}
