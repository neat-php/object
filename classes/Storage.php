<?php

namespace Neat\Object;

use Neat\Database\Query as QueryBuilder;
use Neat\Database\QueryInterface;
use Traversable;

trait Storage
{
    public static function manager(): Manager
    {
        return Manager::get();
    }

    /**
     * @return RepositoryInterface<static>
     */
    public static function repository(): RepositoryInterface
    {
        return static::manager()->repository(static::class);
    }

    /**
     * @param int|string|array $id
     * @return bool
     * @see RepositoryInterface::has()
     */
    public static function has($id): bool
    {
        return static::repository()->has($id);
    }

    /**
     * @param int|string|array $id
     * @return static|null
     * @see RepositoryInterface::get()
     */
    public static function get($id)
    {
        return static::repository()->get($id);
    }

    /**
     * @param string|null $alias
     * @return Query<static>
     * @see RepositoryInterface::select()
     */
    public static function select(string $alias = null): Query
    {
        return static::repository()->select($alias);
    }

    /**
     * @param QueryBuilder|string|array|null $conditions
     * @return QueryBuilder
     * @see RepositoryInterface::query()
     */
    public static function query($conditions = null): QueryBuilder
    {
        return static::repository()->query($conditions);
    }

    /**
     * @param string $sql
     * @param mixed  ...$data
     * @return SQLQuery<static>
     */
    public static function sql(string $sql, ...$data): SQLQuery
    {
        return static::repository()->sql($sql, ...$data);
    }

    /**
     * @param QueryInterface|string|array|null $conditions
     * @return static|null
     * @see RepositoryInterface::one()
     */
    public static function one($conditions = null)
    {
        return static::repository()->one($conditions);
    }

    /**
     * @param QueryInterface|string|array|null $conditions
     * @return list<static>
     * @see RepositoryInterface::all()
     */
    public static function all($conditions = null): array
    {
        return static::repository()->all($conditions);
    }

    /**
     * @param QueryInterface|string|array|null $conditions
     * @return Collection<static>
     * @see RepositoryInterface::collection()
     */
    public static function collection($conditions = null): Collection
    {
        return static::repository()->collection($conditions);
    }

    /**
     * @param QueryInterface|string|array|null $conditions
     * @return Traversable<int, static>
     * @see RepositoryInterface::iterate()
     */
    public static function iterate($conditions = null): Traversable
    {
        return static::repository()->iterate($conditions);
    }

    /**
     * @return void
     * @see RepositoryInterface::store()
     */
    public function store(): void
    {
        $this::repository()->store($this);
    }

    /**
     * @return $this
     * @see RepositoryInterface::load()
     */
    public function load(): self
    {
        return $this::repository()->load($this);
    }

    /**
     * @return void
     * @see RepositoryInterface::delete()
     */
    public function delete(): void
    {
        $this::repository()->delete($this);
    }
}
