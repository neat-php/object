<?php

namespace Neat\Object;

trait EntityTrait
{
    /**
     * @var EntityManager
     */
    public static $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public static function setEntityManager(EntityManager $entityManager)
    {
        static::$entityManager = $entityManager;
    }

    /**
     * @return EntityManager
     */
    public static function getEntityManager(): EntityManager
    {
        return static::$entityManager ?: EntityTrait::$entityManager;
    }

    /**
     * @return Repository
     */
    protected static function repository(): Repository
    {
        return static::getRepository(static::getEntityManager(), static::getTableName(), static::getIdentifier());
    }

    /**
     * @param EntityManager $entityManager
     * @param string $tableName
     * @param string|array $identifier
     * @return Repository
     */
    protected static function getRepository(EntityManager $entityManager, string $tableName, $identifier): Repository
    {
        return new Repository($entityManager, $tableName, $identifier);
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
     * @param array $rows
     * @return array
     */
    public static function createFromRows(array $rows)
    {
        return array_map(['static', 'createFromArray'], $rows);
    }

    /**
     * Finds an model by it's primary key, pass an array in case of an composed key
     *
     * @param integer|array $id
     * @return static|null
     */
    public static function findById($id)
    {
        $result = static::repository()
            ->findById($id);

        return static::createFromArray($result->row());
    }

    /**
     * @param array|string $where
     * @param null|string $orderBy
     * @return ArrayCollection
     */
    public static function findAll($where, $orderBy = null)
    {
        $result = static::repository()
            ->findAll($where, $orderBy);

        return static::collection(static::createFromRows($result->rows()));
    }

    /**
     * @param array $array
     * @return ArrayCollection
     */
    protected static function collection(array $array)
    {
        return new ArrayCollection($array);
    }

    public function store()
    {
        $repository = static::repository();
        $identifier = $this->identifier();
        if ($identifier && $repository->exists($identifier)) {
            $repository->update($identifier, $this->toArray());
        } else {
            $id         = $repository->create($this->toArray());
            $identifier = $this->identifierProperties();
            if ($id && count($identifier) === 1) {
                /** @var Property $property */
                $property = array_shift($identifier);
                $property->set($this, $id);
            }
        }

    }

    public function identifier()
    {
        $identifier = $this->identifierProperties();
        if (count($identifier) === 1) {
            $property = reset($identifier);

            return $property->get($this);
        }

        return array_map(function (Property $property) {
            return $property->get($this);
        }, $identifier);
    }

    /**
     * @return \ReflectionProperty[]
     */
    protected function identifierProperties()
    {
        /** @var Property[] $properties */
        $properties = Property::list(static::class);
        $identifier = static::getIdentifier();

        if (is_array($identifier)) {
            return array_filter($properties, function ($key) use ($identifier) {
                return in_array($key, $identifier);
            }, ARRAY_FILTER_USE_KEY);
        }

        return [$identifier => $properties[$identifier]];
    }

    /**
     * @return string
     */
    protected static function getTableName()
    {
        $path = explode('\\', static::class);

        return strtolower(array_pop($path));
    }

    /**
     * @return string
     */
    protected static function getIdentifier()
    {
        return 'id';
    }
}
