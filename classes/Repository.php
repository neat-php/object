<?php

namespace Neat\Object;

use Neat\Database\Query;
use Neat\Database\Result;

class Repository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Model
     */
    private $entity;

    /**
     * @var \ReflectionClass
     */
    private $reflection;

    public function __construct(EntityManager $entityManager, string $entity)
    {
        $this->entityManager = $entityManager;
        $this->entity = $entity;
        $this->reflection = new \ReflectionClass($this->entity);
        if (!$this->hasMethod('fromArray') ||
            !$this->hasMethod('toArray')) {
            throw new \RuntimeException("Entity $this->entity doesn't have the required methods fromArray and toArray");
        }
    }

    /**
     * @param int|string|array $id
     * @return Result
     */
    public function findById($id): Result
    {
        if (is_array($this->getIdentifier()) && is_array($id)) {
            return $this->findAll($id);
        }
        if (is_array($this->getIdentifier())) {
            throw new \RuntimeException("Entity $this->entity has a composed key, finding by id requires an array, $id given");
        }
        if (is_array($id)) {
            $printed = print_r($id, true);
            throw new \RuntimeException("Entity $this->entity doesn't have a composed key, finding by id requires an int or string, $printed given");
        }

        return $this->findAll([$this->getIdentifier() => $id]);
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
    public function findAll($conditions, string $orderBy = null): Result
    {
        $query = $this->query()
            ->where($conditions);

        if ($orderBy) {
            $query->orderBy($orderBy);
        }

        return $query->query();
    }

    public function query()
    {
        // @TODO add an alias for advanced querying
        $query = $this->entityManager->getConnection()->select('*')
            ->from($this->getTableName());

        return $query;
    }

    private function getTableName(): string
    {
        if ($this->hasMethod('getTableName')) {
            return $this->entity::getTableName();
        }

        $path = explode('\\', $this->entity);

        return strtolower(array_pop($path));
    }

    /**
     * @return array|string
     */
    private function getIdentifier()
    {
        if ($this->hasMethod('getIdentifier')) {
            return $this->entity::getIdentifier();
        }

        return 'id';
    }

    private function hasMethod(string $methodName): bool
    {
        return $this->reflection->hasMethod($methodName);
    }
}