<?php

namespace Neat\Object;


trait EntityTrait
{
    /**
     * @var EntityManager
     */
    public static $entityManager;

    /**
     * @param mixed $entityManager
     */
    public static function setEntityManager(EntityManager $entityManager)
    {
        EntityTrait::$entityManager = $entityManager;
    }

    public static function getEntityManager(): EntityManager
    {
        return EntityTrait::$entityManager;
    }

    /**
     * @param array|null $row
     * @return static|null
     */
    public static function createFromArray($row)
    {
        if (!$row) {
            return null;
        }

        $static = new static();
        $static->fromArray($row);

        return $static;
    }

    /**
     * Finds an model by it's primary key, pass an array in case of an composed key
     *
     * @param integer|array $id
     * @return static|null
     */
    public static function findById($id)
    {
        $result = static::getRepository()
            ->findById($id);

        return static::createFromArray($result->row());
    }

    /**
     * @param array|string $where
     * @param null|string $orderBy
     * @return static[]
     */
    public static function findAll($where, $orderBy = null)
    {

        $result = static::getRepository()
            ->findAll($where, $orderBy);

        return array_map([static::class, 'createFromArray'], $result->rfows());
    }

    protected static function getRepository()
    {
        return static::$entityManager->getRepository(static::class);
    }
}