<?php

/** @noinspection PhpPrivateFieldCanBeLocalVariableInspection */

namespace Neat\Object\Relations\Reference;

use Neat\Object\Exception\ClassMismatchException;
use Neat\Object\Exception\PropertyNotFoundException;
use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Relations\Reference;
use Neat\Object\RepositoryInterface;

trait Builder
{
    /** @var Reference|null */
    private $resolved;

    /** @var callable|null */
    private $factory;

    /** @var Manager */
    private $manager;

    /** @var Policy */
    private $policy;

    /** @var class-string */
    private $class;

    /** @var string */
    private $localClass;

    /** @var Property */
    private $localKeyProperty;

    /** @var string */
    private $remoteClass;

    /** @var RepositoryInterface */
    private $remoteRepository;

    /** @var Property */
    private $remoteKeyProperty;

    /** @var string */
    private $remoteKeyColumn;

    /**
     * @param Manager      $manager
     * @param class-string $class
     * @param class-string $localClass
     * @param class-string $remoteClass
     * @return void
     */
    protected function init(Manager $manager, string $class, string $localClass, string $remoteClass)
    {
        $this->manager     = $manager;
        $this->class       = $class;
        $this->localClass  = $localClass;
        $this->remoteClass = $remoteClass;

        $this->policy           = $manager->policy();
        $this->remoteRepository = $manager->repository($remoteClass);
    }

    /**
     * This method will resolve or return the reference
     *  When the reference is not yet resolved, first the factory will be handled if available.
     *  Next the build method will be called
     *
     * @return Reference
     */
    public function resolve(): Reference
    {
        if ($this->resolved !== null) {
            return $this->resolved;
        }
        if ($this->factory !== null) {
            ($this->factory)($this);
        }
        $this->resolved = $this->build();
        if (!$this->resolved instanceof $this->class) {
            throw new ClassMismatchException($this->class, get_class($this->resolved));
        }

        return $this->resolved;
    }

    /**
     * Pas a callable which accepts a builder and customizes the reference as necessary
     *
     * @param callable $factory
     * @return $this
     */
    public function factory(callable $factory): self
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Should pass the required parameters to the constructor of the requested class as specified by the class property
     * The constructed reference should be assigned to the resolved property
     *
     * @return Reference
     */
    abstract protected function build(): Reference;

    /**
     * Set's the class of the reference that should be build
     *
     * @param class-string $class
     * @return $this
     */
    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @note Does not check whether column or property exists
     * @param string $localKeyColumn
     * @return $this
     */
    private function initLocalKeyColumn(string $localKeyColumn): self
    {
        $this->localKeyProperty = $this->policy->properties($this->localClass)[$localKeyColumn] ?? null;

        return $this;
    }

    /**
     * @param string $localKeyColumn
     * @return $this
     */
    public function setLocalKeyColumn(string $localKeyColumn): self
    {
        $this->localKeyProperty = $this->propertyByColumn($this->localClass, $localKeyColumn);

        return $this;
    }

    /**
     * @param string $localKeyProperty
     * @return $this
     * @note Passing a Property instance to setLocalKey is deprecated
     * @noinspection PhpMissingParamTypeInspection
     */
    public function setLocalKey($localKeyProperty): self
    {
        if (!$localKeyProperty instanceof Property) {
            $localKeyProperty = $this->property($this->localClass, $localKeyProperty);
        }

        $this->localKeyProperty = $localKeyProperty;

        return $this;
    }


    /**
     * @note Does not check whether column or property exists
     * @param string $remoteKeyColumn
     * @return $this
     */
    private function initRemoteKeyColumn(string $remoteKeyColumn): self
    {
        $this->remoteKeyProperty = $this->policy->properties($this->remoteClass)[$remoteKeyColumn] ?? null;
        $this->remoteKeyColumn   = $remoteKeyColumn;

        return $this;
    }

    /**
     * @param string $remoteKeyColumn
     * @return $this
     * @deprecated Use setRemoteKeyColumn() instead
     */
    public function setRemoteKeyString(string $remoteKeyColumn): self
    {
        return $this->setRemoteKeyColumn($remoteKeyColumn);
    }

    /**
     * @param string $remoteKeyColumn
     * @return $this
     */
    public function setRemoteKeyColumn(string $remoteKeyColumn): self
    {
        $this->remoteKeyProperty = $this->propertyByColumn($this->remoteClass, $remoteKeyColumn);
        $this->remoteKeyColumn   = $remoteKeyColumn;

        return $this;
    }

    /**
     * @param string $remoteKeyProperty
     * @return $this
     * @note Passing a Property instance to setRemoteKey is deprecated
     * @noinspection PhpMissingParamTypeInspection
     */
    public function setRemoteKey($remoteKeyProperty): self
    {
        if (!$remoteKeyProperty instanceof Property) {
            $remoteKeyProperty = $this->property($this->remoteClass, $remoteKeyProperty);
        }

        $this->remoteKeyProperty = $remoteKeyProperty;
        $this->remoteKeyColumn   = $this->policy->column($this->remoteKeyProperty->name());

        return $this;
    }

    /**
     * @param RepositoryInterface $remoteRepository
     * @return self
     */
    public function setRemoteRepository(RepositoryInterface $remoteRepository): self
    {
        $this->remoteRepository = $remoteRepository;

        return $this;
    }

    /**
     * @param class-string $class
     * @return string
     */
    public function keyColumn(string $class): string
    {
        return array_values($this->policy->key($class))[0];
    }

    /**
     * @param class-string $class
     * @return string
     */
    public function foreignKeyColumn(string $class): string
    {
        return $this->policy->foreignKey($class);
    }

    /**
     * @param class-string|object $class
     * @param string              $property
     * @return Property
     */
    public function property($class, string $property): Property
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $column = $this->policy->column($property);
        $properties = $this->policy->properties($class);
        if (!isset($properties[$column])) {
            throw new PropertyNotFoundException($class, $property);
        }

        return $properties[$column];
    }

    /**
     * @param class-string $class
     * @param string       $column
     * @return Property
     */
    public function propertyByColumn(string $class, string $column): Property
    {
        $properties = $this->policy->properties($class);
        if (!isset($properties[$column])) {
            throw new PropertyNotFoundException($class, "for column $column");
        }

        return $properties[$column];
    }
}
