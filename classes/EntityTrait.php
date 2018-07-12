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
    public static function findById($id)
    {
        return static::repository()->findById($id);
    }

    /**
     * Get one by conditions
     *
     * @param Query|string|array|null $conditions
     * @param string|null $orderBy
     * @return static|null
     */
    public static function findOne($conditions, $orderBy = null)
    {
        return static::repository()->findOne($conditions, $orderBy);
    }

    /**
     * Get all by conditions
     *
     * @param Query|string|array|null $conditions
     * @param null|string $orderBy
     * @return static[]
     */
    public static function findAll($conditions = null, $orderBy = null)
    {
        return static::repository()->findAll($conditions, $orderBy);
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
