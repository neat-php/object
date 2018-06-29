<?php

namespace Neat\Object;

trait EntityTrait
{
    /**
     * @return Repository
     */
    public static function repository(): Repository
    {
        return EntityManager::instance()->repository(static::class);
    }

    /**
     * Finds an model by it's primary key, pass an array in case of an composed key
     *
     * @param integer|array $id
     * @return static|null
     */
    public static function findById($id)
    {
        return static::repository()->findById($id);
    }

    /**
     * @param string|array|null $conditions
     * @param string|null $orderBy
     * @return static|null
     */
    public static function findOne($conditions, $orderBy = null)
    {
        return static::repository()->findOne($conditions, $orderBy);
    }

    /**
     * @param array|string $where
     * @param null|string $orderBy
     * @return Collection
     */
    public static function findAll($where, $orderBy = null)
    {
        return static::repository()->findAll($where, $orderBy);
    }

    public function store()
    {
        $this::repository()->store($this);
    }

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        $path = explode('\\', static::class);

        return strtolower(array_pop($path));
    }

    /**
     * @return array
     */
    public static function getKey(): array
    {
        return ['id'];
    }
}
