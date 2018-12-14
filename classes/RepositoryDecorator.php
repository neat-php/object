<?php

namespace Neat\Object;

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
    public function get($id)
    {
        return $this->repository()->get($id);
    }

    /**
     * Create select query
     *
     * @param string $alias Table alias (optional)
     * @return Query
     */
    public function select(string $alias = null): Query
    {
        return $this->repository()->select($alias);
    }

    /**
     * Create select query with conditions
     *
     * @param Query|string|array $conditions Query instance or where clause (optional)
     * @return Query
     */
    public function query($conditions = null): Query
    {
        return $this->repository()->query($conditions);
    }

    /**
     * Get one by conditions
     *
     * @param Query|array|string|null $conditions SQL where clause or Query instance
     * @return mixed|null
     */
    public function one($conditions = null)
    {
        return $this->repository()->one($conditions);
    }

    /**
     * Get all by conditions
     *
     * @param Query|string|array|null $conditions SQL where clause or Query instance
     * @return object[]
     */
    public function all($conditions = null): array
    {
        return $this->repository()->all($conditions);
    }

    /**
     * Get collection of entities by conditions
     *
     * @param Query|string|array|null $conditions SQL where clause or Query instance
     * @return Collection|object[]
     */
    public function collection($conditions = null): Collection
    {
        return $this->repository()->collection($conditions);
    }

    /**
     * Iterate entities by conditions
     *
     * @param Query|string|array|null $conditions SQL where clause or Query instance
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
    public function store($entity)
    {
        $this->repository()->store($entity);
    }

    /**
     * Insert entity data into database table and return inserted id
     *
     * @param array $data
     * @return int
     */
    public function insert(array $data)
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
     * @return false|int
     */
    public function delete($entity)
    {
        return $this->repository()->delete($entity);
    }

    /**
     * Convert to an associative array
     *
     * @param object $entity
     * @return array
     */
    public function toArray($entity): array
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
    public function fromArray($entity, array $data)
    {
        return $this->repository()->fromArray($entity, $data);
    }

    /**
     * Create entity from row
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->repository()->create($data);
    }

    /**
     * Get identifier for entity
     *
     * @param object $entity
     * @return array
     */
    public function identifier($entity)
    {
        return $this->repository()->identifier($entity);
    }
}
