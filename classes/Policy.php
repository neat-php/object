<?php

namespace Neat\Object;

use Neat\Database\Connection;
use Neat\Object\Decorator\CreatedAt;
use Neat\Object\Decorator\SoftDelete;
use Neat\Object\Decorator\UpdatedAt;
use Neat\Object\Exception\ClassNotFoundException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;

class Policy
{
    /**
     * Get repository
     *
     * @param string     $class
     * @param Connection $connection
     * @return RepositoryInterface
     */
    public function repository(string $class, Connection $connection): RepositoryInterface
    {
        $properties = $this->properties($class);
        $table      = $this->table($class);
        $key        = $this->key($class);

        $repository = new Repository($connection, $class, $table, $key, $properties);
        if ($softDelete = $this->softDelete($class)) {
            $repository = new SoftDelete($repository, $softDelete, $properties[$softDelete]);
        }
        if ($createdStamp = $this->createdStamp($class)) {
            $repository = new CreatedAt($repository, $createdStamp, $properties[$createdStamp]);
        }
        if ($updatedStamp = $this->updatedStamp($class)) {
            $repository = new UpdatedAt($repository, $updatedStamp, $properties[$updatedStamp]);
        }

        return $repository;
    }

    /**
     * Get table name for class
     *
     * @param string $class
     * @return string
     */
    public function table(string $class): string
    {
        $path = explode('\\', $class);

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', array_pop($path)));
    }

    /**
     * Get junction table name for two classes
     *
     * @param string $classA
     * @param string $classB
     * @return string
     */
    public function junctionTable(string $classA, string $classB): string
    {
        $tables = array_map([$this, 'table'], [$classA, $classB]);
        sort($tables);

        return array_shift($tables) . '_' . array_shift($tables);
    }

    /**
     * Get column name for property
     *
     * @param string $property
     * @return string
     */
    public function column(string $property): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property));
    }

    /**
     * Get column name for a foreign key
     *
     * @param string $class
     * @return string
     */
    public function foreignKey(string $class): string
    {
        return $this->table($class) . '_id';
    }

    /**
     * Get properties for class
     *
     * @param string $class
     * @return Property[]
     */
    public function properties(string $class): array
    {
        try {
            $reflection = new ReflectionClass($class);
        } catch (ReflectionException $e) {
            throw new ClassNotFoundException($class);
        }

        $properties = [];
        foreach ($reflection->getProperties() as $reflection) {
            $property = $this->property($reflection);
            if ($this->skip($property)) {
                continue;
            }

            $properties[$this->column($property->name())] = $property;
        }

        return $properties;
    }

    /**
     * @param ReflectionProperty $reflection
     * @return Property
     */
    public function property(ReflectionProperty $reflection): Property
    {
        if (preg_match('/\\s@var\\s([\\w\\\\]+)(?:\\|null)?\\s/', $reflection->getDocComment(), $matches)) {
            $type = ltrim($matches[1], '\\');
            switch ($type) {
                case 'bool':
                case 'boolean':
                    return new Property\Boolean($reflection);
                case 'int':
                case 'integer':
                    return new Property\Integer($reflection);
                case 'DateTime':
                    return new Property\DateTime($reflection);
                case 'DateTimeImmutable':
                    return new Property\DateTimeImmutable($reflection);
            }
        }

        return new Property($reflection);
    }

    /**
     * Skip property?
     *
     * @param Property $property
     * @return bool
     */
    public function skip(Property $property): bool
    {
        return $property->static() || preg_match('/\\s@nostorage\\s/', $property->comment());
    }

    /**
     * Get delete stamp property
     *
     * @param string $class
     * @return string|null
     */
    public function softDelete(string $class)
    {
        return property_exists($class, 'deletedAt') ? $this->column('deletedAt') : null;
    }

    /**
     * Get create stamp property
     *
     * @param string $class
     * @return string|null
     */
    public function createdStamp(string $class)
    {
        return property_exists($class, 'createdAt') ? $this->column('createdAt') : null;
    }

    /**
     * Get create stamp property
     *
     * @param string $class
     * @return string|null
     */
    public function updatedStamp(string $class)
    {
        return property_exists($class, 'updatedAt') ? $this->column('updatedAt') : null;
    }

    /**
     * Get key property names
     *
     * @param string $class
     * @return string[]
     */
    public function key(string $class): array
    {
        if (defined($class . '::KEY')) {
            return (array) constant($class . '::KEY');
        }

        if (property_exists($class, 'id')) {
            return ['id'];
        }

        throw new RuntimeException('Unable to determine the key');
    }
}
