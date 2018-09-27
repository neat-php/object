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
        return Manager::instance();
    }

    /**
     * Get repository
     *
     * @return Repository
     */
    public static function repository(): Repository
    {
        return self::manager()->repository(static::class);
    }

    /**
     * @see Repository::has()
     * @param int|string|array $id
     * @return bool
     */
    public static function has($id): bool
    {
        return static::repository()->has($id);
    }

    /**
     * @see Repository::get()
     * @param int|string|array $id
     * @return static|null
     */
    public static function get($id)
    {
        return static::repository()->get($id);
    }

    /**
     * @see Repository::select()
     * @param string|null $alias
     * @return Query
     */
    public static function select(string $alias = null): Query
    {
        return static::repository()->select($alias);
    }

    /**
     * @see Repository::query()
     * @param Query|string|array|null $conditions
     * @return Query
     */
    public static function query($conditions = null): Query
    {
        return static::repository()->query($conditions);
    }

    /**
     * @see Repository::one()
     * @param Query|string|array|null $conditions
     * @return static|null
     */
    public static function one($conditions = null)
    {
        return static::repository()->one($conditions);
    }

    /**
     * @see Repository::all()
     * @param Query|string|array|null $conditions
     * @return static[]
     */
    public static function all($conditions = null): array
    {
        return static::repository()->all($conditions);
    }

    /**
     * @see Repository::collection()
     * @param Query|string|array|null $conditions
     * @return Collection|static[]
     */
    public static function collection($conditions = null): Collection
    {
        return static::repository()->collection($conditions);
    }

    /**
     * @see Repository::iterate()
     * @param Query|string|array|null $conditions
     * @return Traversable|static[]
     */
    public static function iterate($conditions = null): Traversable
    {
        return static::repository()->iterate($conditions);
    }

    /**
     * @see Repository::store()
     */
    public function store()
    {
        $this::repository()->store($this);
    }

    /**
     * @see Repository::delete()
     */
    public function delete()
    {
        $this::repository()->delete($this);
    }
}
