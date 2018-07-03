<?php

namespace Neat\Object;

use Neat\Database\Connection;
use Neat\Database\Query;
use Neat\Database\Result;

class Repository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $entity;

    /**
     * @var string
     */
    private $table;

    /**
     * @var array
     */
    private $key;

    /**
     * @var Property
     */
    private $properties;

    /**
     * Repository constructor.
     * @param Connection $connection
     * @param string $entity
     * @param string|null $table
     * @param Property[] $key
     * @param string[] $properties
     */
    public function __construct(Connection $connection, string $entity, string $table, array $key, array $properties)
    {
        $this->connection = $connection;
        $this->entity     = $entity;
        $this->table      = $table;
        $this->key        = $key;
        $this->properties = $properties;
    }

    /**
     * @param int|string|array $id
     * @return boolean
     */
    public function exists($id): bool
    {
        return $this->connection
                ->select('count(1)')->from($this->table)->where($this->where($id))->limit(1)
                ->query()->value() === '1';
    }

    /**
     * @param int|string|array $id
     * @return mixed
     */
    public function findById($id)
    {
        return $this->findOne($this->where($id));
    }

    /**
     * @param Query|array|string $conditions
     * @param string|null $orderBy
     * @return mixed
     */
    public function findOne($conditions = null, string $orderBy = null)
    {
        $query = $conditions instanceof Query ? $conditions : $this->query();

        if ($conditions && !$conditions instanceof Query) {
            $query->where($conditions);
        }

        $query->limit(1);

        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        $result = $query->query();
        $row    = $result->row();

        if (!$row) {
            return null;
        }

        return $this->createFromRow($row);
    }

    /**
     * @param Query|string|array|null $conditions
     * @param string|null $orderBy
     * @return array
     */
    public function findAll($conditions = null, string $orderBy = null): array
    {
        $result = $this->find($conditions, $orderBy);

        return $this->createFromRows($result->rows());
    }

    /**
     * @param Query|string|array|null $conditions
     * @return Collection
     */
    public function collection($conditions = null): Collection
    {
        return new Collection($this->findAll($conditions));
    }

    /**
     * @param Query|string|array|null $conditions
     * @param string|null $orderBy
     * @return \Generator
     */
    public function iterateAll($conditions = null, string $orderBy = null)
    {
        $result = $this->find($conditions, $orderBy);
        foreach ($result as $row) {
            yield $this->createFromRow($row);
        }
    }

    /**
     * @param Query|string|array $conditions
     * @param string|null $orderBy
     * @return Result
     */
    public function find($conditions = null, string $orderBy = null): Result
    {
        if ($conditions instanceof Query) {
            $query = $conditions;
        } else {
            $query = $this->query();
        }
        if ($conditions && !$conditions instanceof Query) {
            $query->where($conditions);
        }

        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        return $query->query();
    }

    /**
     * @param string|null $alias
     * @return Query
     */
    public function query(string $alias = null)
    {
        $query = $this->connection
            ->select('*')->from($this->table, $alias);

        return $query;
    }

    public function store($entity)
    {
        $data       = $this->toArray($entity);
        $identifier = $this->identifier($entity);
        if ($identifier && array_filter($identifier) && $this->exists($identifier)) {
            $this->update($identifier, $data);
        } else {
            $id = $this->create($data);
            if ($id && count($this->key) === 1) {
                $this->properties[reset($this->key)]->set($entity, $id);
            }
        }
    }

    /**
     * @param array $data
     * @return int
     */
    public function create(array $data)
    {
        $this->connection
            ->insert($this->table, $data);

        return $this->connection->insertedId();
    }

    /**
     * @param int|string|array $id
     * @param array $data
     * @return false|int
     */
    public function update($id, array $data)
    {
        return $this->connection
            ->update($this->table, $data, $this->where($id));
    }

    /**
     * Converts to an associative array
     *
     * @param object $entity
     * @return array
     */
    public function toArray($entity): array
    {
        $array = [];
        foreach ($this->properties as $key => $property) {
            $array[$key] = $property->get($entity);
        }

        return $array;
    }

    /**
     * Converts from an associative array
     *
     * @param $entity
     * @param array $array
     * @return mixed
     */
    public function fromArray($entity, $array)
    {
        foreach ($this->properties as $key => $property) {
            $property->set($entity, $array[$key] ?? null);
        }

        return $entity;
    }

    /**
     * Instantiates an entity with the row data
     *
     * @param array $array
     * @return mixed
     */
    public function createFromRow(array $array)
    {
        return $this->fromArray(new $this->entity, $array);
    }

    /**
     * @param array $rows
     * @return array
     */
    public function createFromRows(array $rows): array
    {
        return array_map([$this, 'createFromRow'], $rows);
    }

    /**
     * @param $entity
     * @return array
     */
    private function identifier($entity)
    {
        $keys = array_combine($this->key, $this->key);

        return array_map(function (string $key) use ($entity) {
            return $this->properties[$key]->get($entity);
        }, $keys);
    }

    /**
     * Validates the identifier to prevent unexpected behaviour
     *
     * @param int|string|array $id
     */
    private function validateIdentifier($id)
    {
        $printed = print_r($id, true);
        if (count($this->key) > 1 && !is_array($id)) {
            throw new \RuntimeException("Entity $this->entity has a composed key, finding by id requires an array, given: $printed");
        }
        if (is_array($id) && count($this->key) !== count($id)) {
            $keys = print_r($this->key, true);
            throw new \RuntimeException("Entity $this->entity requires the following keys: $keys, given: $printed");
        }
    }

    /**
     * Creates the where condition for the identifier
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
