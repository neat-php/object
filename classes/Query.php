<?php

namespace Neat\Object;

use Neat\Database\Connection;

class Query extends \Neat\Database\Query
{
    use QueryRepository;

    /**
     * Query constructor
     *
     * @param Connection $connection
     * @param Repository $repository
     */
    public function __construct(Connection $connection, Repository $repository)
    {
        parent::__construct($connection);

        $this->repository = $repository;
    }
}
