<?php

namespace Neat\Object\Relations\Reference;

use Neat\Object\Exception\ClassNotFoundException;
use Neat\Object\Exception\NonExistingProperty;
use Neat\Object\Manager;
use Neat\Object\Policy;
use Neat\Object\Property;
use Neat\Object\Relations\Reference;

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

    /** @var string */
    private $class;

    /**
     * @param Manager $manager
     * @param Policy  $policy
     * @param string  $class
     * @return void
     */
    protected function init(Manager $manager, Policy $policy, string $class)
    {
        $this->manager = $manager;
        $this->policy  = $policy;
        $this->class   = $class;
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
            // throw
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
     * @param string $class
     * @return $this
     */
    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @param string|object $class
     * @param string        $property
     * @return Property
     */
    public function property($class, string $property): Property
    {
        if (!is_object($class) && !class_exists($class)) {
            throw new ClassNotFoundException($class);
        }
        if (!property_exists($class, $property)) {
            throw new NonExistingProperty($class, $property);
        }
        $properties = $this->policy->properties($class);

        return $properties[$this->policy->column($property)];
    }
}
