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
     * @see Repository::one()
     * @return mixed|null
     */
    public function one()
    {
        return $this->repository->one($this);
    }

    /**
     * @see Repository::all()
     * @return array
     */
    public function all(): array
    {
        return $this->repository->all($this);
    }

    /**
     * @see Repository::collection()
     * @return Collection|array
     */
    public function collection(): Collection
    {
        return $this->repository->collection($this);
    }


    /**
     * @see Repository::iterate()
     * @return Generator|array
     */
    public function iterate(): Generator
    {
        return $this->repository->iterate($this);
    }
}
