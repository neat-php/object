<?php

namespace Neat\Object;

use Neat\Database\Query;

trait EntityTrait
{
    /**
     * Get repository
     *
     * @return Repository
     */
    public static function repository(): Repository
    {
        return Manager::instance()->repository(static::class);
    }

    /**
     * Find by id or key(s)
     *
     * @param integer|array $id
     * @return static|null
     */
    public static function get($id)
    {
        return static::repository()->get($id);
    }

    /**
     * Get one by conditions
     *
     * @param Query|string|array|null $conditions
     * @return static|null
     */
    public static function one($conditions)
    {
        return static::repository()->one($conditions);
    }

    /**
     * Get all by conditions
     *
     * @param Query|string|array|null $conditions
     * @return static[]
     */
    public static function all($conditions = null)
    {
        return static::repository()->all($conditions);
    }

    /**
     * Get collection of entities by conditions
     *
     * @param Query|string|array|null $conditions
     * @return Collection
     */
    public static function collection($conditions = null)
    {
        return static::repository()->collection($conditions);
    }

    /**
     * Store entity to the database
     */
    public function store()
    {
        $this::repository()->store($this);
    }
}
