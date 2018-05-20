<?php

namespace Neat\Object;

use Neat\Database\Result;

class Repository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string|array
     */
    private $identifier;

    /**
     * Repository constructor.
     * @param EntityManager $entityManager
     * @param string|null $tableName
     * @param mixed|null $identifier
     */
    public function __construct(EntityManager $entityManager, string $tableName, $identifier)
    {
        $this->entityManager = $entityManager;
        $this->tableName     = $tableName;
        $this->identifier    = $identifier;
    }

    /**
     * @param int|string|array $id
     * @return boolean
     */
    public function exists($id): bool
    {
        return $this->entityManager->getConnection()
                ->select('count(1)')->from($this->tableName)->where($this->where($id))->limit(1)
                ->query()->value() === '1';
    }

    /**
     * @param int|string|array $id
     * @return Result
     */
    public function findById($id): Result
    {
        return $this->findOne($this->where($id));
    }

    /**
     * @param array|string $conditions
     * @param string|null $orderBy
     * @return Result
     */
    public function findOne($conditions, string $orderBy = null): Result
    {
        $query = $this->query()
            ->where($conditions)
            ->limit(1);

        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        return $query->query();
    }

    /**
     * @param string|[] $query
     * @param string|null $orderBy
     * @return Result
     */
    public function findAll($conditions = null, string $orderBy = null): Result
    {
        $query = $this->query();
        if ($conditions) {
            $query->where($conditions);
        }

        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        return $query->query();
    }

    /**
     * @return \Neat\Database\Query
     */
    public function query()
    {
        // @TODO add an alias for advanced querying
        $query = $this->entityManager->getConnection()
            ->select('*')->from($this->tableName);

        return $query;
    }

    /**
     * @param array $data
     * @return int
     */
    public function create(array $data)
    {
        $this->entityManager->getConnection()
            ->insert($this->tableName, $data);

        return $this->entityManager->getConnection()->insertedId();
    }

    /**
     * @param int|string|array $id
     * @param array $data
     * @return false|int
     */
    public function update($id, array $data)
    {
        return $this->entityManager->getConnection()
            ->update($this->tableName, $data, $this->where($id));
    }

    /**
     * Validates the identifier to prevent unexpected behaviour
     *
     * @param int|string|array $id
     */
    public function validateIdentifier($id)
    {
        $printed = print_r($id, true);
        if (is_array($this->identifier) && !is_array($id)) {
            throw new \RuntimeException("Entity $this->tableName has a composed key, finding by id requires an array, given: $printed");
        }
        if (is_array($id) && !is_array($this->identifier)) {
            throw new \RuntimeException("Entity $this->tableName doesn't have a composed key, finding by id requires an int or string, given: $printed");
        }
    }

    /**
     * Creates the where condition for the identifier
     *
     * @param int|string|array $id
     * @return array
     */
    public function where($id)
    {
        $this->validateIdentifier($id);

        if (!is_array($id)) {
            return [$this->identifier => $id];
        } else {
            return $id;
        }
    }
}
