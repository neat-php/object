<?php

namespace Neat\Object;

use Traversable;

interface RepositoryInterface
{
    /**
     * Has entity with identifier?
     *
     * @param int|string|array $id Identifier value(s)
     * @return bool
     */
    public function has($id): bool;

    /**
     * Get entity by identifier?
     *
     * @param int|string|array $id Identifier value(s)
     * @return mixed|null
     */
    public function get($id);

    /**
     * Create select query
     *
     * @param string $alias Table alias (optional)
     * @return Query
     */
    public function select(string $alias = null): Query;

    /**
     * Create select query with conditions
     *
     * @param Query|string|array $conditions Query instance or where clause (optional)
     * @return Query
     */
    public function query($conditions = null): Query;

    /**
     * Get one by conditions
     *
     * @param Query|array|string|null $conditions SQL where clause or Query instance
     * @return mixed|null
     */
    public function one($conditions = null);

    /**
     * Get all by conditions
     *
     * @param Query|string|array|null $conditions SQL where clause or Query instance
     * @return object[]
     */
    public function all($conditions = null): array;

    /**
     * Get collection of entities by conditions
     *
     * @param Query|string|array|null $conditions SQL where clause or Query instance
     * @return Collection|object[]
     */
    public function collection($conditions = null): Collection;

    /**
     * Iterate entities by conditions
     *
     * @param Query|string|array|null $conditions SQL where clause or Query instance
     * @return Traversable|object[]
     */
    public function iterate($conditions = null): Traversable;

    /**
     * Store entity to the database
     *
     * @param object $entity
     */
    public function store($entity);

    /**
     * Insert entity data into database table and return inserted id
     *
     * @param array $data
     * @return int
     */
    public function insert(array $data);

    /**
     * Update entity data in database table
     *
     * @param int|string|array $id
     * @param array            $data
     * @return false|int
     */
    public function update($id, array $data);

    /**
     * @param object $entity
     * @return false|int
     */
    public function delete($entity);

    /**
     * Convert to an associative array
     *
     * @param object $entity
     * @return array
     */
    public function toArray($entity): array;

    /**
     * Convert from an associative array
     *
     * @param object $entity
     * @param array  $data
     * @return mixed
     */
    public function fromArray($entity, array $data);

    /**
     * Create entity from row
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Get identifier for entity
     *
     * @param object $entity
     * @return array
     */
    public function identifier($entity);
}
