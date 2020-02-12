<?php

namespace Neat\Object\Repository;

use Neat\Database\Connection;
use Neat\Database\Query as QueryBuilder;
use Neat\Database\QueryInterface;
use Neat\Object\Collection;
use Neat\Object\Property\Property;
use Neat\Object\Query;
use Neat\Object\Relation\Relation;
use Neat\Object\RepositoryInterface;
use RuntimeException;
use Traversable;

class Repository implements RepositoryInterface
{
    /** @var Connection */
    private $connection;

    /** @var string */
    private $class;

    /** @var string */
    private $table;

    /** @var string[] */
    private $key;

    /** @var Property[] */
    private $properties;

    /**
     * Repository constructor
     *
     * @param Connection $connection The connection to the database the entity table exists in
     * @param string     $class The class name of the entity the repository is meant for
     * @param string     $table The table name for the entity
     * @param string[]   $key The primary key columns for the table, pass multiple items for a composed key
     * @param Property[] $properties The properties of the entity, should only include properties which actually map to
     * a database column
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
     * @inheritDoc
     */
    public function has($id): bool
    {
        $identifier = $this->where($id);

        return $this->connection->select('count(1)')->from($this->table)->where($identifier)->limit(1)
                ->query()->value() === '1';
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $identifier = $this->where($id);

        return $this->one($identifier);
    }

    /**
     * @inheritDoc
     */
    public function select(string $alias = null): Query
    {
        $quotedTable = $this->connection->quoteIdentifier($this->table);

        $query = new Query($this->connection, $this);
        $query->select(($alias ?? $quotedTable) . '.*')->from($this->table, $alias);

        return $query;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function one($conditions = null)
    {
        if (!$conditions instanceof QueryBuilder && $conditions instanceof QueryInterface) {
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
     * @inheritDoc
     */
    public function all($conditions = null): array
    {
        if (!$conditions instanceof QueryBuilder && $conditions instanceof QueryInterface) {
            $rows = $conditions->query()->rows();
        } else {
            $rows = $this->query($conditions)->query()->rows();
        }

        return array_map([$this, 'create'], $rows);
    }

    /**
     * @inheritDoc
     */
    public function collection($conditions = null): Collection
    {
        $objects = $this->all($conditions);

        return new Collection($objects);
    }

    /**
     * @inheritDoc
     */
    public function iterate($conditions = null): Traversable
    {
        if (!$conditions instanceof QueryBuilder && $conditions instanceof QueryInterface) {
            $result = $conditions->query();
        } else {
            $result = $this->query($conditions)->query();
        }
        foreach ($result as $row) {
            yield $this->create($row);
        }
    }

    /**
     * @inheritDoc
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
     * @return void
     */
    private function setRelations(array $relations)
    {
        foreach ($relations as $key => $relation) {
            if (strpos($key, 'belongsToOne') !== false) {
                $relation->store();
            }
        }
    }

    /**
     * @param Relation[] $relations
     * @return void
     */
    private function storeRelations(array $relations)
    {
        foreach ($relations as $key => $relation) {
            if (strpos($key, 'belongsToOne') === false) {
                $relation->store();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function insert(array $data): int
    {
        $this->connection->insert($this->table, $data);

        return $this->connection->insertedId();
    }

    /**
     * @inheritDoc
     */
    public function update($id, array $data)
    {
        $where = $this->where($id);

        return $this->connection->update($this->table, $data, $where);
    }

    /**
     * @inheritDoc
     */
    public function delete($entity)
    {
        $identifier = $this->identifier($entity);
        $where      = $this->where($identifier);

        return $this->connection->delete($this->table, $where);
    }

    /**
     * @inheritDoc
     */
    public function load($entity)
    {
        $identifier = array_filter($this->identifier($entity));
        if (!$identifier) {
            return $entity;
        }
        $where = $this->where($identifier);
        $row   = $this->select()->where($where)->query()->row();
        if (!$row) {
            return $entity;
        }
        $this->fromArray($entity, $row);

        return $entity;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function fromArray($entity, array $data)
    {
        foreach ($this->properties as $key => $property) {
            $property->set($entity, $data[$key] ?? null);
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data)
    {
        return $this->fromArray(new $this->class(), $data);
    }

    /**
     * @inheritDoc
     */
    public function identifier($entity)
    {
        $keys = array_combine($this->key, $this->key);

        return array_map(
            function (string $key) use ($entity) {
                return $this->properties[$key]->get($entity);
            },
            $keys
        );
    }

    /**
     * Validate the identifier to prevent unexpected behaviour
     *
     * @param int|string|array $id
     * @return void
     */
    private function validateIdentifier($id)
    {
        $printed = print_r($id, true);
        if (count($this->key) > 1 && !is_array($id)) {
            throw new RuntimeException(
                "Entity $this->class has a composed key, finding by id requires an array, given: $printed"
            );
        }
        if (is_array($id) && count($this->key) !== count($id)) {
            $keys = print_r($this->key, true);
            throw new RuntimeException("Entity $this->class requires the following keys: $keys, given: $printed");
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
        /** @var string $key */
        $key = reset($this->key);

        if (!is_array($id)) {
            return [$key => $id];
        } else {
            return $id;
        }
    }
}
