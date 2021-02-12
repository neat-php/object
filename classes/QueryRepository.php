<?php

namespace Neat\Object;

use Traversable;

trait QueryRepository
{
    /** @var RepositoryInterface */
    private $repository;

    /**
     * @return object|null
     * @see RepositoryInterface::one()
     */
    public function one(): ?object
    {
        return $this->repository->one($this);
    }

    /**
     * @return object[]
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
