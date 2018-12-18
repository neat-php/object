<?php

namespace Neat\Object;

use Neat\Database\Connection;
use Neat\Database\Query as QueryBuilder;
use Neat\Database\QueryInterface;
use Neat\Database\SQLQuery;
use Neat\Object\Relations\Relation;
use Traversable;

class Repository implements RepositoryInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string[]
     */
    private $key;

    /**
     * @var Property[]
     */
    private $properties;

    /**
     * Repository constructor
     *
     * @param Connection $connection
     * @param string     $class
     * @param string     $table
     * @param string[]   $key
     * @param Property[] $properties
     */
    public function __construct(Connection $connection, string $class, string $table, array $key, array $properties)
    {
        $this->connection = $connection;
        $this->class      = $class;
        $this->table      = $table;
        $this->key        = $key;
        $this->properties = $properties;
    }

    /**
     * Has entity with identifier?
     *
     * @param int|string|array $id Identifier value(s)
     * @return bool
     */
    public function has($id): bool
    {
        return $this->connection
                ->select('count(1)')->from($this->table)->where($this->where($id))->limit(1)
                ->query()->value() === '1';
    }

    /**
     * Get entity by identifier?
     *
     * @param int|string|array $id Identifier value(s)
     * @return mixed|null
     */
    public function get($id)
    {
        return $this->one($this->where($id));
    }

    /**
     * Create select query
     *
     * @param string $alias Table alias (optional)
     * @return Query
     */
    public function select(string $alias = null): Query
    {
        $quotedTable = $this->connection->quoteIdentifier($this->table);

        $query = new Query($this->connection, $this);
        $query->select(($alias ?? $quotedTable) . '.*')
            ->from($this->table, $alias);

        return $query;
    }

    /**
     * Create select query with conditions
     *
     * @param QueryBuilder|string|array $conditions Query instance or where clause (optional)
     * @return QueryBuilder
     */
    public function query($conditions = null): QueryBuilder
    {
        if ($conditions instanceof QueryBuilder) {
            return $conditions;
        }

        $query = $this->select();
        if ($conditions) {
            $query->where($conditions);
        }

        return $query;
    }

    /**
     * Get one by conditions
     *
     * @param QueryInterface|array|string|null $conditions SQL where clause or Query instance
     * @return mixed|null
     */
    public function one($conditions = null)
    {
        if ($conditions instanceof SQLQuery) {
            $row = $conditions->query()->row();
        } else {
            $row = $this->query($conditions)->limit(1)->query()->row();
        }
        if (!$row) {
            return null;
        }

        return $this->create($row);
    }

    /**
     * Get all by conditions
     *
     * @param QueryInterface|string|array|null $conditions SQL where clause or Query instance
     * @return object[]
     */
    public function all($conditions = null): array
    {
        if ($conditions instanceof SQLQuery) {
            $rows = $conditions->query()->rows();
        } else {
            $rows = $this->query($conditions)->query()->rows();
        }

        return array_map([$this, 'create'], $rows);
    }

    /**
     * Get collection of entities by conditions
     *
     * @param QueryInterface|string|array|null $conditions SQL where clause or Query instance
     * @return Collection|object[]
     */
    public function collection($conditions = null): Collection
    {
        $objects = $this->all($conditions);

        return new Collection($objects);
    }

    /**
     * Iterate entities by conditions
     *
     * @param QueryInterface|string|array|null $conditions SQL where clause or Query instance
     * @return Traversable|object[]
     */
    public function iterate($conditions = null): Traversable
    {
        if ($conditions instanceof SQLQuery) {
            $result = $conditions->query();
        } else {
            $result = $this->query($conditions)->query();
        }
        foreach ($result as $row) {
            yield $this->create($row);
        }
    }

    /**
     * Store entity to the database
     *
     * @param object $entity
     */
    public function store($entity)
    {
        if (method_exists($entity, 'relations')) {
            $this->setRelations($entity->relations()->all());
        }
        $data       = $this->toArray($entity);
        $identifier = $this->identifier($entity);
        if ($identifier && array_filter($identifier) && $this->has($identifier)) {
            $this->update($identifier, $data);
        } else {
            $id = $this->insert($data);
            if ($id && count($this->key) === 1) {
                $this->properties[reset($this->key)]->set($entity, $id);
            }
        }
        if (method_exists($entity, 'relations')) {
            $this->storeRelations($entity->relations()->all());
        }
    }

    /**
     * @param Relation[] $relations
     */
    private function setRelations(array $relations)
    {
        foreach ($relations as $key => $relation) {
            if (strpos($key, 'belongsToOne') !== false) {
                $relation->store();
            }
        }
    }

    private function storeRelations(array $relations)
    {
        foreach ($relations as $key => $relation) {
            if (strpos($key, 'belongsToOne') === false) {
                $relation->store();
            }
        }
    }

    /**
     * Insert entity data into database table and return inserted id
     *
     * @param array $data
     * @return int
     */
    public function insert(array $data)
    {
        $this->connection
            ->insert($this->table, $data);

        return $this->connection->insertedId();
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
        return $this->connection
            ->update($this->table, $data, $this->where($id));
    }

    /**
     * @param object $entity
     * @return false|int
     */
    public function delete($entity)
    {
        $identifier = $this->identifier($entity);

        return $this->connection
            ->delete($this->table, $this->where($identifier));
    }

    /**
     * Convert to an associative array
     *
     * @param object $entity
     * @return array
     */
    public function toArray($entity): array
    {
        $data = [];
        foreach ($this->properties as $key => $property) {
            $data[$key] = $property->get($entity);
        }

        return $data;
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
        foreach ($this->properties as $key => $property) {
            $property->set($entity, $data[$key] ?? null);
        }

        return $entity;
    }

    /**
     * Create entity from row
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->fromArray(new $this->class, $data);
    }

    /**
     * Get identifier for entity
     *
     * @param object $entity
     * @return array
     */
    public function identifier($entity)
    {
        $keys = array_combine($this->key, $this->key);

        return array_map(function (string $key) use ($entity) {
            return $this->properties[$key]->get($entity);
        }, $keys);
    }

    /**
     * Validate the identifier to prevent unexpected behaviour
     *
     * @param int|string|array $id
     */
    private function validateIdentifier($id)
    {
        $printed = print_r($id, true);
        if (count($this->key) > 1 && !is_array($id)) {
            throw new \RuntimeException("Entity $this->class has a composed key, finding by id requires an array, given: $printed");
        }
        if (is_array($id) && count($this->key) !== count($id)) {
            $keys = print_r($this->key, true);
            throw new \RuntimeException("Entity $this->class requires the following keys: $keys, given: $printed");
        }
    }

    /**
     * Get where condition for identifier
     *
     * @param int|string|array $id
     * @return array
     */
    private function where($id)
    {
        $this->validateIdentifier($id);
        $key = reset($this->key);

        if (!is_array($id)) {
            return [$key => $id];
        } else {
            return $id;
        }
    }
}
