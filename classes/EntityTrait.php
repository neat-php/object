<?php

namespace Neat\Object;

use Neat\Database\Query;

trait EntityTrait
{
    /**
     * @return Repository
     */
    public static function repository(): Repository
    {
        return Manager::instance()->repository(static::class);
    }

    /**
     * Finds an model by it's primary key, pass an array in case of a composed key
     *
     * @param integer|array $id
     * @return static|null
     */
    public static function findById($id)
    {
        return static::repository()->findById($id);
    }

    /**
     * @param Query|string|array|null $conditions
     * @param string|null $orderBy
     * @return static|null
     */
    public static function findOne($conditions, $orderBy = null)
    {
        return static::repository()->findOne($conditions, $orderBy);
    }

    /**
     * @param Query|string|array|null $conditions
     * @param null|string $orderBy
     * @return static[]
     */
    public static function findAll($conditions = null, $orderBy = null)
    {
        return static::repository()->findAll($conditions, $orderBy);
    }

    /**
     * @param Query|string|array|null $conditions
     * @return Collection
     */
    public static function collection($conditions = null)
    {
        return static::repository()->collection($conditions);
    }

    public function store()
    {
        $this::repository()->store($this);
    }
}
