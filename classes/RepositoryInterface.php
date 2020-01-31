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
    public function get($id): ?object;

    /**
     * Create select query
     *
     * @param string $alias Table alias
     * @return Query
     */
    public function select(?string $alias = null): Query;

    /**
     * Create select query with conditions
     *
     * @param Query|string|array $conditions Query instance or where clause (optional)
     * @return Query
     */
    public function query($conditions = null): \Neat\Database\Query;

    /**
     * Get one by conditions
     *
     * @param QueryInterface|array|string|null $conditions SQL where clause or Query instance
     * @return mixed|null
     */
    public function one($conditions = null): ?object;

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
     */
    public function store(object $entity): void;

    /**
     * Insert entity data into database table and return inserted id
     *
     * @param array $data
     * @return int
     */
    public function insert(array $data): int;

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
     * @return object
     */
    public function load(object $entity): object;

    /**
     * @param object $entity
     * @return false|int
     */
    public function delete(object $entity);

    /**
     * Convert to an associative array
     *
     * @param object $entity
     * @return array
     */
    public function toArray(object $entity): array;

    /**
     * Convert from an associative array
     *
     * @param object $entity
     * @param array  $data
     * @return object
     */
    public function fromArray(object $entity, array $data): object;

    /**
     * Create entity from row
     *
     * @param array $data
     * @return object
     */
    public function create(array $data): object;

    /**
     * Get identifier for entity
     *
     * @param object $entity
     * @return array
     */
    public function identifier(object $entity): array;
}
