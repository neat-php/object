<?php

namespace Neat\Object;

use Neat\Database\Connection;
use Traversable;

class Query extends \Neat\Database\Query
{
    /** @var RepositoryInterface */
    private $repository;

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
     * @return mixed|null
     * @see RepositoryInterface::one()
     */
    public function one()
    {
        return $this->repository->one($this);
    }

    /**
     * @return array
     * @see RepositoryInterface::all()
     */
    public function all(): array
    {
        return $this->repository->all($this);
    }

    /**
     * @return Collection
     * @see RepositoryInterface::collection()
     */
    public function collection(): Collection
    {
        return $this->repository->collection($this);
    }

    /**
     * @return Traversable
     * @see RepositoryInterface::iterate()
     */
    public function iterate(): Traversable
    {
        return $this->repository->iterate($this);
    }
}
