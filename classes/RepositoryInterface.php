<?php

namespace Neat\Object;

use Neat\Database\Query as QueryBuilder;
use Neat\Database\QueryInterface;
use Traversable;

interface RepositoryInterface
{
    /**
     * Get repository decorator layer by class name
     *
     * @template T of RepositoryInterface
     * @param class-string<T> $class
     * @return T
     */
    public function layer(string $class): RepositoryInterface;

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
     * @return object|null
     */
    public function get($id): ?object;

    /**
     * Create select query
     *
     * @param string|null $alias Table alias (optional)
     * @return Query
     */
    public function select(string $alias = null): Query;

    /**
     * Create select query with conditions
     *
     * @param QueryBuilder|Query|string|array|null $conditions Query instance or where clause (optional)
     * @return QueryBuilder|Query
     */
    public function query($conditions = null): QueryBuilder;

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
     * @return object|null
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
     * @return void
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
     * @return int
     */
    public function update($id, array $data): int;

    /**
     * @param object $entity
     * @return mixed
     */
    public function load(object $entity);

    /**
     * @param object $entity
     * @return int
     */
    public function delete(object $entity): int;

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
