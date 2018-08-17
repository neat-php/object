<?php

namespace Neat\Object;

use Generator;
use Neat\Database\Connection;

class Query extends \Neat\Database\Query
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * Query constructor.
     * @param Connection $connection
     * @param Repository $repository
     */
    public function __construct(Connection $connection, Repository $repository)
    {
        parent::__construct($connection);
        $this->repository = $repository;
    }

    /**
     * @return mixed|null
     */
    public function one()
    {
        return $this->repository->one($this);
    }

    /**
     * @return object[]
     */
    public function all()
    {
        return $this->repository->all($this);
    }

    /**
     * @return Collection|object[]
     */
    public function collection()
    {
        return $this->repository->collection($this);
    }

    /**
     * @return Generator|object[]
     */
    public function iterate()
    {
        yield from $this->repository->iterate($this);
    }
}
