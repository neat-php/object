<?php

namespace Neat\Object;

use Neat\Database\Connection;
use Neat\Object\Decorator\CreatedAt;
use Neat\Object\Decorator\EventDispatcher;
use Neat\Object\Decorator\OrderBy;
use Neat\Object\Decorator\SoftDelete;
use Neat\Object\Decorator\UpdatedAt;
use Neat\Object\Exception\ClassNotFoundException;
use Psr\EventDispatcher\EventDispatcherInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;

class Policy
{
    /** @var EventDispatcherInterface|null */
    private $dispatcher;

    /** @var callable|null @psalm-var callable(string):string|null */
    private $pluralize;
    /**
     * Policy constructor
     *
     * @param EventDispatcherInterface|null $dispatcher
     * @param callable|null                 $pluralize
     * @psalm-param callable(string):string|null $pluralize
     */
    public function __construct(EventDispatcherInterface $dispatcher = null, callable $pluralize = null)
    {
        $this->dispatcher = $dispatcher;
        $this->pluralize  = $pluralize;
    }

    /**
     * Get repository
     *
     * @param class-string $class
     * @param Connection   $connection
     * @return RepositoryInterface
     */
    public function repository(string $class, Connection $connection): RepositoryInterface
    {
        $properties = $this->properties($class);
        $table      = $this->table($class);
        $key        = $this->key($class);
        $factory    = $this->factory($class);

        $repository = new Repository($connection, $class, $table, $key, $properties, $factory);
        if ($orderBy = $this->orderBy($class)) {
            $repository = new OrderBy($repository, $orderBy);
        }
        if ($softDelete = $this->softDelete($class)) {
            $repository = new SoftDelete($repository, $softDelete, $properties[$softDelete]);
        }
        if ($createdStamp = $this->createdStamp($class)) {
            $repository = new CreatedAt($repository, $createdStamp, $properties[$createdStamp]);
        }
        if ($updatedStamp = $this->updatedStamp($class)) {
            $repository = new UpdatedAt($repository, $updatedStamp, $properties[$updatedStamp]);
        }
        if ($this->dispatcher && $events = $this->events($class)) {
            $repository = new EventDispatcher($repository, $this->dispatcher, $events);
        }

        return $repository;
    }

    /**
     * Get table name for class
     *
     * @param class-string $class
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
     * @param class-string $classA
     * @param class-string $classB
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
     * @param class-string $class
     * @return string
     */
    public function foreignKey(string $class): string
    {
        return $this->table($class) . '_id';
    }

    /**
     * Get properties for class
     *
     * @param class-string $class
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
     * Get factory method
     *
     * @param class-string $class
     * @return callable|null
     */
    public function factory(string $class): ?callable
    {
        return method_exists($class, 'createFromArray') ? [$class, 'createFromArray'] : null;
    }

    /**
     * Get event classes
     *
     * @param class-string $class
     * @return string[]
     */
    public function events(string $class): array
    {
        if (defined($class . '::EVENTS')) {
            return (array)constant($class . '::EVENTS');
        }

        return [];
    }

    /**
     * Get default order by
     *
     * @param string $class
     * @return string|null
     */
    public function orderBy(string $class): ?string
    {
        if (defined($class . '::ORDER_BY')) {
            return constant($class . '::ORDER_BY');
        }

        return null;
    }

    /**
     * Get delete stamp property
     *
     * @param class-string $class
     * @return string|null
     */
    public function softDelete(string $class): ?string
    {
        return property_exists($class, 'deletedAt') ? $this->column('deletedAt') : null;
    }

    /**
     * Get create stamp property
     *
     * @param class-string $class
     * @return string|null
     */
    public function createdStamp(string $class): ?string
    {
        return property_exists($class, 'createdAt') ? $this->column('createdAt') : null;
    }

    /**
     * Get create stamp property
     *
     * @param class-string $class
     * @return string|null
     */
    public function updatedStamp(string $class): ?string
    {
        return property_exists($class, 'updatedAt') ? $this->column('updatedAt') : null;
    }

    /**
     * Get accessor relation method
     *
     * @param string $operation
     * @param string $relation
     * @return string
     */
    public function accessorRelationMethod(string $operation, string $relation): string
    {
        if ($this->pluralize && in_array(strtolower($operation), ['add', 'has', 'remove'])) {
            $relation = ($this->pluralize)($relation);
        }

        return lcfirst($relation);
    }

    /**
     * Get key property names
     *
     * @param class-string $class
     * @return string[]
     */
    public function key(string $class): array
    {
        if (defined($class . '::KEY')) {
            return (array)constant($class . '::KEY');
        }

        if (property_exists($class, 'id')) {
            return ['id'];
        }

        throw new RuntimeException('Unable to determine the key');
    }
}
