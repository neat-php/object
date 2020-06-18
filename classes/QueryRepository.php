<?php

namespace Neat\Object;

use Traversable;

trait QueryRepository
{
    /** @var RepositoryInterface */
    private $repository;

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
