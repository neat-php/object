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
     * @var self
     */
    private $entity;

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
     * @param string $entity
     * @param string|null $tableName
     * @param mixed|null $identifier
     */
    public function __construct(
        EntityManager $entityManager,
        string $entity,
        string $tableName = null,
        $identifier = null
    ) {
        $this->entityManager = $entityManager;
        $this->entity        = $entity;
        $this->tableName     = $tableName ?: $this->getTableName();
        $this->identifier    = $identifier ?: $this->getIdentifier();
    }

    /**
     * @param int|string|array $id
     * @return Result
     */
    public function findById($id): Result
    {
        if (is_array($this->identifier) && is_array($id)) {
            return $this->findOne($id);
        }
        if (is_array($this->identifier)) {
            $printed = print_r($id, true);
            throw new \RuntimeException("Entity $this->entity has a composed key, finding by id requires an array, given: $printed");
        }
        if (is_array($id)) {
            $printed = print_r($id, true);
            throw new \RuntimeException("Entity $this->entity doesn't have a composed key, finding by id requires an int or string, given: $printed");
        }

        return $this->findOne([$this->identifier => $id]);
    }

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

    public function query()
    {
        // @TODO add an alias for advanced querying
        $query = $this->entityManager->getConnection()->select('*')
            ->from($this->tableName);

        return $query;
    }

    public function getTableName(): string
    {
        $path = explode('\\', $this->entity);

        return strtolower(array_pop($path));
    }

    /**
     * @return array|string
     */
    public function getIdentifier()
    {
        return 'id';
    }
}
