<?php

namespace Neat\Object;

use Neat\Database\QueryInterface;
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
    public function query($conditions = null): \Neat\Database\Query;

    /**
     * @param string $sql
     * @param mixed  ...$data
     * @return SQLQuery
     */
    public function sql(string $sql, ...$data): SQLQuery;

    /**
     * Get one by conditions
     *
     * @param QueryInterface|array|string|null $conditions SQL where clause or Query instance
     * @return mixed|null
     */
    public function one($conditions = null);

    /**
     * Get all by conditions
     *
     * @param QueryInterface|string|array|null $conditions SQL where clause or Query instance
     * @return object[]
     */
    public function all($conditions = null): array;

    /**
     * Get collection of entities by conditions
     *
     * @param QueryInterface|string|array|null $conditions SQL where clause or Query instance
     * @return Collection|object[]
     */
    public function collection($conditions = null): Collection;

    /**
     * Iterate entities by conditions
     *
     * @param QueryInterface|string|array|null $conditions SQL where clause or Query instance
     * @return Traversable|object[]
     */
    public function iterate($conditions = null): Traversable;

    /**
     * Store entity to the database
     *
     * @param object $entity
     * @return void
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
     * @return int
     */
    public function update($id, array $data);

    /**
     * @param object $entity
     * @return mixed
     */
    public function load($entity);

    /**
     * @param object $entity
     * @return int
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
