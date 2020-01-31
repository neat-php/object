<?php

namespace Neat\Object;

use Neat\Database\Connection;
use Traversable;

class Query extends \Neat\Database\Query
{
    private RepositoryInterface $repository;

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

    /**
     * @see RepositoryInterface::one()
     * @return mixed|null
     */
    public function one()
    {
        return $this->repository->one($this);
    }

    /**
     * @see RepositoryInterface::all()
     * @return array
     */
    public function all(): array
    {
        return $this->repository->all($this);
    }

    /**
     * @see RepositoryInterface::collection()
     * @return Collection|array
     */
    public function collection(): Collection
    {
        return $this->repository->collection($this);
    }

    /**
     * @see RepositoryInterface::iterate()
     * @return Traversable|array
     */
    public function iterate(): Traversable
    {
        return $this->repository->iterate($this);
    }
}
