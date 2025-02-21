<?php

namespace Neat\Object;

use Neat\Database\Query as QueryBuilder;
use Traversable;

trait RepositoryDecorator
{
    abstract protected function repository(): RepositoryInterface;

    /**
     * @inheritDoc
     */
    public function layer(string $class): RepositoryInterface
    {
        if ($this instanceof $class) {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return $this;
        }

        return $this->repository()->layer($class);
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        return $this->repository()->has($id);
    }

    /**
     * @inheritDoc
     */
    public function get($id): ?object
    {
        return $this->repository()->get($id);
    }

    /**
     * @inheritDoc
     */
    public function select(?string $alias = null): Query
    {
        return $this->repository()->select($alias);
    }

    /**
     * @inheritDoc
     */
    public function query($conditions = null): QueryBuilder
    {
        return $this->repository()->query($conditions);
    }

    /**
     * @inheritDoc
     */
    public function sql(string $sql, ...$data): SQLQuery
    {
        return $this->repository()->sql($sql, ...$data);
    }

    /**
     * @inheritDoc
     */
    public function one($conditions = null): ?object
    {
        return $this->repository()->one($conditions);
    }

    /**
     * @inheritDoc
     */
    public function all($conditions = null): array
    {
        return $this->repository()->all($conditions);
    }

    /**
     * @inheritDoc
     */
    public function collection($conditions = null): Collection
    {
        return $this->repository()->collection($conditions);
    }

    /**
     * @inheritDoc
     */
    public function iterate($conditions = null): Traversable
    {
        return $this->repository()->iterate($conditions);
    }

    /**
     * @inheritDoc
     */
    public function store(object $entity): void
    {
        $this->repository()->store($entity);
    }

    /**
     * @inheritDoc
     */
    public function insert(array $data): int
    {
        return $this->repository()->insert($data);
    }

    /**
     * @inheritDoc
     */
    public function update($id, array $data): int
    {
        return $this->repository()->update($id, $data);
    }

    /**
     * @inheritDoc
     */
    public function load(object $entity): object
    {
        return $this->repository()->load($entity);
    }

    /**
     * @inheritDoc
     */
    public function delete(object $entity): int
    {
        return $this->repository()->delete($entity);
    }

    /**
     * @inheritDoc
     */
    public function toArray(object $entity): array
    {
        return $this->repository()->toArray($entity);
    }

    /**
     * @inheritDoc
     */
    public function fromArray(object $entity, array $data): object
    {
        return $this->repository()->fromArray($entity, $data);
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): object
    {
        return $this->repository()->create($data);
    }

    /**
     * @inheritDoc
     */
    public function identifier(object $entity): array
    {
        return $this->repository()->identifier($entity);
    }
}
