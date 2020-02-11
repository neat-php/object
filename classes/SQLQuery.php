<?php

namespace Neat\Object;

use Neat\Database\Connection;

class SQLQuery extends \Neat\Database\SQLQuery
{
    use QueryRepository;

    /**
     * SQLQuery constructor
     *
     * @param Connection $connection
     * @param Repository $repository
     * @param string     $sql
     */
    public function __construct(Connection $connection, Repository $repository, string $sql)
    {
        parent::__construct($connection, $sql);

        $this->repository = $repository;
    }
}
