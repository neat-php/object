<?php

namespace Neat\Object;

use Neat\Database\Query as QueryBuilder;
use Neat\Database\QueryInterface;
use Traversable;

trait RepositoryDecorator
{
    abstract protected function repository(): RepositoryInterface;

    /**
     * Has entity with identifier?
     *
     * @param int|string|array $id Identifier value(s)
     * @return bool
     */
    public function has($id): bool
    {
        return $this->repository()->has($id);
    }

    /**
     * Get entity by identifier?
     *
     * @param int|string|array $id Identifier value(s)
     * @return mixed|null
     */
    public function get($id): ?object
    {
        return $this->repository()->get($id);
    }

    /**
     * Create select query
     *
     * @param string|null $alias Table alias (optional)
     * @return Query
     */
    public function select(?string $alias = null): Query
    {
        return $this->repository()->select($alias);
    }

    /**
     * Create select query with conditions
     *
     * @param QueryBuilder|string|array $conditions Query instance or where clause (optional)
     * @return QueryBuilder
     */
    public function query($conditions = null): QueryBuilder
    {
        return $this->repository()->query($conditions);
    }

    /**
     * Get one by conditions
     *
     * @param QueryInterface|array|string|null $conditions SQL where clause or Query instance
     * @return mixed|null
     */
    public function one($conditions = null): ?object
    {
        return $this->repository()->one($conditions);
    }

    /**
     * Get all by conditions
     *
     * @param QueryInterface|string|array|null $conditions SQL where clause or Query instance
     * @return object[]
     */
    public function all($conditions = null): array
    {
        return $this->repository()->all($conditions);
    }

    /**
     * Get collection of entities by conditions
     *
     * @param QueryInterface|string|array|null $conditions SQL where clause or Query instance
     * @return Collection|object[]
     */
    public function collection($conditions = null): Collection
    {
        return $this->repository()->collection($conditions);
    }

    /**
     * Iterate entities by conditions
     *
     * @param QueryInterface|string|array|null $conditions SQL where clause or Query instance
     * @return Traversable|object[]
     */
    public function iterate($conditions = null): Traversable
    {
        return $this->repository()->iterate($conditions);
    }

    /**
     * Store entity to the database
     *
     * @param object $entity
     */
    public function store(object $entity): void
    {
        $this->repository()->store($entity);
    }

    /**
     * Insert entity data into database table and return inserted id
     *
     * @param array $data
     * @return int
     */
    public function insert(array $data): int
    {
        return $this->repository()->insert($data);
    }

    /**
     * Update entity data in database table
     *
     * @param int|string|array $id
     * @param array            $data
     * @return false|int
     */
    public function update($id, array $data)
    {
        return $this->repository()->update($id, $data);
    }

    /**
     * @param object $entity
     * @return object
     */
    public function load(object $entity): object
    {
        return $this->repository()->load($entity);
    }

    /**
     * @param object $entity
     * @return false|int
     */
    public function delete(object $entity)
    {
        return $this->repository()->delete($entity);
    }

    /**
     * Convert to an associative array
     *
     * @param object $entity
     * @return array
     */
    public function toArray(object $entity): array
    {
        return $this->repository()->toArray($entity);
    }

    /**
     * Convert from an associative array
     *
     * @param object $entity
     * @param array  $data
     * @return mixed
     */
    public function fromArray(object $entity, array $data): object
    {
        return $this->repository()->fromArray($entity, $data);
    }

    /**
     * Create entity from row
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data): object
    {
        return $this->repository()->create($data);
    }

    /**
     * Get identifier for entity
     *
     * @param object $entity
     * @return array
     */
    public function identifier(object $entity): array
    {
        return $this->repository()->identifier($entity);
    }
}
