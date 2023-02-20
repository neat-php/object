<?php

namespace Neat\Object;

use Traversable;

/**
 * @template T of object
 */
trait QueryRepository
{
    /** @var RepositoryInterface<T> */
    private $repository;

    /**
     * @return T|null
     * @see RepositoryInterface::one()
     */
    public function one(): ?object
    {
        return $this->repository->one($this);
    }

    /**
     * @return list<T>
     * @see RepositoryInterface::all()
     */
    public function all(): array
    {
        return $this->repository->all($this);
    }

    /**
     * @return Collection<T>
     * @see RepositoryInterface::collection()
     */
    public function collection(): Collection
    {
        return $this->repository->collection($this);
    }

    /**
     * @return Traversable<int, T>
     * @see RepositoryInterface::iterate()
     */
    public function iterate(): Traversable
    {
        return $this->repository->iterate($this);
    }
}
