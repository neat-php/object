<?php

namespace Neat\Object;

use Neat\Database\Connection;

class Query extends \Neat\Database\Query
{
    use QueryRepository;

    /**
     * Query constructor
     *
     * @param Connection          $connection
     * @param RepositoryInterface $repository
     */
    public function __construct(Connection $connection, RepositoryInterface $repository)
    {
        parent::__construct($connection);

        $this->repository = $repository;
    }
}
