<?php

namespace Neat\Object;

use Neat\Database\Connection;

/**
 * @template T of object
 */
class SQLQuery extends \Neat\Database\SQLQuery
{
    /** @use QueryRepository<T> */
    use QueryRepository;

    /**
     * SQLQuery constructor
     *
     * @param Connection             $connection
     * @param RepositoryInterface<T> $repository
     * @param string                 $sql
     */
    public function __construct(Connection $connection, RepositoryInterface $repository, string $sql)
    {
        parent::__construct($connection, $sql);

        $this->repository = $repository;
    }
}
