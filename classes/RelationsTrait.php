<?php

namespace Neat\Object;

/**
 * TODO:
 *   - Remove Storage dependency and use query builder instead
 *   - Make relations with an composed key possible
 *   - We should actually return an relation builder to make relations more agile
 *   - Add hasOrCreateOne for example an stockArticle should always be returned for a given article but should not be able to be set
 * @package System\Model
 */
trait RelationsTrait
{
    /**
     * Relation cache
     *
     * @var mixed[]
     */
    private $relations;

    /**
     * Development helper, pass the given class and the expected instance to validate it
     *
     * @param string $class
     * @param string $instance
     * @param string $error
     * @throws \RuntimeException
     */
    private function expectInstance(string $class, string $instance, $error = ' if used in an relation')
    {
        if (!class_exists($class)) {
            throw new \RuntimeException("Class '$class' does not exist");
        } elseif (!in_array($instance, class_parents($class))) {
            throw new \RuntimeException("Class '$class' should be an instance of '$instance'$error");
        }
    }

    /**
     * We can only handle relations on existing classes and entities,
     * let's check if the class exists and whether it's an entity or not
     *
     * @param string $class
     * @throws \RuntimeException
     */
    private function expectEntity(string $class)
    {
        $this->expectInstance($class, Entity::class);
    }

    /**
     * @param string $class
     * @param string $property The local identifier, the foreign key
     * @param Entity|null|false $value If null or an value overwrite the value
     * @return object|mixed|null
     */
    protected function belongsToOne(string $class, string $property = null, $value = false)
    {
        $this->expectEntity($class);

        if (!$property) {
            $property = $this->guessIdProperty($class);
        }

        $cacheKey = "belongsToOne_{$class}";
        if ($value || is_null($value)) {
            if (is_null($value)) {
                if (isset($this->relations[$cacheKey])) {
                    unset($this->relations[$cacheKey]);
                }
                return $this->{$property} = null;
            }
            $this->relations[$cacheKey] = $value;

            return $this->{$property} = $this->getIdentifier($value);
        }

        if (!$this->{$property}) {
            return null;
        }

        return $this->cache($cacheKey, function () use ($class, $property) {
            return $class::findById($this->{$property});
        });
    }

    /**
     * @inProgress
     * @param string $class
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @param string|null $arrayClass
     * @return mixed
     */
    protected function belongsToMany(string $class, $foreignKey = null, $localKey = null, $arrayClass = null)
    {
        $this->expectEntity($class);
        if ($arrayClass) {
            $this->expectInstance($arrayClass, ArrayCollection::class, '');
        } else {
            $arrayClass = ArrayCollection::class;
        }

        $foreignKey = $foreignKey ?: $this->guessForeignKey();

        $localKey = $localKey ?: $this->getIdentifierProperty($class);

        $cacheKey = "belongsToMany_{$class}_{$arrayClass}";
        return $this->cache($cacheKey, function () use ($class, $foreignKey, $localKey, $arrayClass) {
            $results = Storage::getStorage($class)->findAll([$foreignKey => $this->{$localKey}]);

            return new $arrayClass($results, $class, $this);
        });
    }

    /**
     * @param string $class
     * @param string $foreignKey
     * @param Entity|null|false $value
     * @return Entity|mixed|null
     */
    protected function hasOne(string $class, string $foreignKey = null, $value = false)
    {
        $this->expectEntity($class);

        $cacheKey = "hasOne_{$class}";
        if ($value || is_null($value)) {
            if (is_null($value)) {
                if (isset($this->relations[$cacheKey])) {
                    unset($this->relations[$cacheKey]);
                }
                return $value->{$property} = null;
            }
            $this->relations[$cacheKey] = $value;

            return $value->{$property} = $this->getIdentifier($this);
        }

        $foreignKey = $foreignKey ?: $this->guessForeignKey();

        if (!$this->{$foreignKey}) {
            return null;
        }

        return $this->cache($cacheKey, function () use ($class, $foreignKey) {
            return Storage::getStorage($class)->find([$foreignKey => $this->getPrimaryKey($this)]);
        });
    }

    /**
     * @param string $class
     * @param string|null $foreignKey
     * @param string|null $localKey
     * @param string|null $arrayClass
     * @return mixed|array|ArrayCollection
     */
    protected function hasMany(string $class, $foreignKey = null, $localKey = null, $arrayClass = null)
    {
        $this->expectEntity($class);
        if ($arrayClass) {
            $this->expectInstance($arrayClass, ArrayCollection::class, '');
        } else {
            $arrayClass = ArrayCollection::class;
        }

        $foreignKey = $foreignKey ?: $this->guessForeignKey();

        $localKey = $localKey ?: $this->getPrimaryKey($this);

        $cacheKey = "hasMany_{$class}_{$arrayClass}";
        return $this->cache($cacheKey, function () use ($class, $foreignKey, $localKey, $arrayClass) {
            $results = Storage::getStorage($class)->findAll([$foreignKey => $this->{$localKey}]);

            return new $arrayClass($results, $class, $this);
        });
    }

    /**
     * @param string|null $key
     * @param callable $callback
     * @return mixed
     */
    private function cache(string $key = null, callable $callback)
    {
        if (is_null($key)) {
            return $callback();
        }

        if (!isset($this->relations[$key])) {
            $this->relations[$key] = $callback();
        }

        return $this->relations[$key];
    }

    /**
     * @param Entity|object $entity
     * @return mixed
     */
    protected function getIdentifier($entity)
    {
        $identifierProperty = $this->getIdentifierProperty($entity);

        return $entity->{$identifierProperty};
    }

    /**
     * @param Entity|object|string $entity
     * @return string
     */
    protected function getIdentifierProperty($entity)
    {
        // The compound database key, for example 'supplier_id,article_id'
        $dbKeyString = $entity::STORAGE_KEY;
        $dbKeys = explode(',', $dbKeyString);

        if (count($dbKeys) > 1) {
            throw new \RuntimeException("");
        }

        // The property keys, for example [supplierId, articleId]
        $propertyKeys = array_map([$this, 'snakeToCamelCase'], $dbKeys);
        $propertyKey = array_shift($propertyKeys);

        return $propertyKey;
    }

    protected function guessIdProperty($class)
    {
        $baseClass = strtolower(basename(str_replace('\\', '/', $class)));

        return "{$baseClass}Id";
    }

    /**
     * @param Entity|string $entity An entity instance or a class name of an entity
     * @return string
     */
    protected function getPrimaryKey($entity): string
    {
        // The compound database key, for example 'supplier_id,article_id'
        $dbKeys = $entity::getIdentifier();

        return is_array($dbKeys) ? array_shift($dbKeys) : $dbKeys;
    }

    /**
     * Determines the foreign key name based on the convention class name + id:
     * Article --> article_id
     * Order --> order_id
     *
     * @return string
     */
    protected function guessForeignKey()
    {
        $baseClass = strtolower($this->classBasename());

        return "{$baseClass}_id";
    }

    /**
     * Returns only the class name and not the full namespace
     *
     * @return string
     */
    protected function classBasename()
    {
        $class = get_class($this);

        return basename(str_replace('\\', '/', $class));
    }

    /**
     * @param string $string
     * @return string
     */
    protected function snakeToCamelCase(string $string): string
    {
        $parts = explode('_', $string);

        foreach ($parts as $index => $part) {
            if ($index === 0) {
                continue;
            }
            $parts[$index] = ucfirst($part);
        }

        return implode('', $parts);
    }

    /**
     * @param string $string
     * @return string
     */
    protected function camelToSnakeCase(string $string): string
    {
        if (preg_match_all('/(^|[A-Z])+([a-z0-9]|$)*/', $string, $matches)) {
            $words = [];
            foreach ($matches[0] as $key => $word) {
                if (strlen($word) > 0) {
                    $words[] = strtolower($word);
                }
            }

            return implode('_', $words);
        } else {
            return strtolower($string);
        }
    }
}
