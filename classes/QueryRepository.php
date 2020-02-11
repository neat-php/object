<?php

namespace Neat\Object;

use Traversable;

trait QueryRepository
{
    /** @var Repository */
    private $repository;

    /**
     * @return mixed|null
     * @see Repository::one()
     */
    public function one()
    {
        return $this->repository->one($this);
    }

    /**
     * @return array
     * @see Repository::all()
     */
    public function all(): array
    {
        return $this->repository->all($this);
    }

    /**
     * @return Collection
     * @see Repository::collection()
     */
    public function collection(): Collection
    {
        return $this->repository->collection($this);
    }

    /**
     * @return Traversable
     * @see Repository::iterate()
     */
    public function iterate(): Traversable
    {
        return $this->repository->iterate($this);
    }
}
