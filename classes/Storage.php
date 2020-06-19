<?php

namespace Neat\Object;

use Traversable;

trait Storage
{
    /**
     * @return Manager
     */
    public static function manager(): Manager
    {
        return Manager::get();
    }

    /**
     * Get repository
     *
     * @return RepositoryInterface
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
     * @return Query
     * @see RepositoryInterface::select()
     */
    public static function select(string $alias = null): Query
    {
        return static::repository()->select($alias);
    }

    /**
     * @param Query|string|array|null $conditions
     * @return Query
     * @see RepositoryInterface::query()
     */
    public static function query($conditions = null): Query
    {
        return static::repository()->query($conditions);
    }

    /**
     * @param string $sql
     * @param mixed  ...$data
     * @return SQLQuery
     */
    public static function sql(string $sql, ...$data): SQLQuery
    {
        return static::repository()->sql($sql, ...$data);
    }

    /**
     * @param Query|string|array|null $conditions
     * @return static|null
     * @see RepositoryInterface::one()
     */
    public static function one($conditions = null)
    {
        return static::repository()->one($conditions);
    }

    /**
     * @param Query|string|array|null $conditions
     * @return static[]
     * @see RepositoryInterface::all()
     */
    public static function all($conditions = null): array
    {
        return static::repository()->all($conditions);
    }

    /**
     * @param Query|string|array|null $conditions
     * @return Collection
     * @see RepositoryInterface::collection()
     */
    public static function collection($conditions = null): Collection
    {
        return static::repository()->collection($conditions);
    }

    /**
     * @param Query|string|array|null $conditions
     * @return Traversable
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
    public function store()
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
    public function delete()
    {
        $this::repository()->delete($this);
    }
}
