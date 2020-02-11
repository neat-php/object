<?php

namespace Neat\Object\Repository;

use Neat\Database\Query as QueryBuilder;
use Neat\Object\Collection;
use Neat\Object\Query;
use Neat\Object\RepositoryInterface;
use Traversable;

trait RepositoryDecorator
{
    abstract protected function repository(): RepositoryInterface;

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return $this->repository()->has($id);
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->repository()->get($id);
    }

    /**
     * @inheritDoc
     */
    public function select(string $alias = null): Query
    {
        return $this->repository()->select($alias);
    }

    /**
     * @inheritDoc
     */
    public function query($conditions = null): QueryBuilder
    {
        return $this->repository()->query($conditions);
    }

    /**
     * @inheritDoc
     */
    public function one($conditions = null)
    {
        return $this->repository()->one($conditions);
    }

    /**
     * @inheritDoc
     */
    public function all($conditions = null): array
    {
        return $this->repository()->all($conditions);
    }

    /**
     * @inheritDoc
     */
    public function collection($conditions = null): Collection
    {
        return $this->repository()->collection($conditions);
    }

    /**
     * @inheritDoc
     */
    public function iterate($conditions = null): Traversable
    {
        return $this->repository()->iterate($conditions);
    }

    /**
     * @inheritDoc
     */
    public function store($entity)
    {
        $this->repository()->store($entity);
    }

    /**
     * @inheritDoc
     */
    public function insert(array $data)
    {
        return $this->repository()->insert($data);
    }

    /**
     * @inheritDoc
     */
    public function update($id, array $data)
    {
        return $this->repository()->update($id, $data);
    }

    /**
     * @inheritDoc
     */
    public function load($entity)
    {
        return $this->repository()->load($entity);
    }

    /**
     * @inheritDoc
     */
    public function delete($entity)
    {
        return $this->repository()->delete($entity);
    }

    /**
     * @inheritDoc
     */
    public function toArray($entity): array
    {
        return $this->repository()->toArray($entity);
    }

    /**
     * @inheritDoc
     */
    public function fromArray($entity, array $data)
    {
        return $this->repository()->fromArray($entity, $data);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        return $this->repository()->create($data);
    }

    /**
     * @inheritDoc
     */
    public function identifier($entity)
    {
        return $this->repository()->identifier($entity);
    }
}
