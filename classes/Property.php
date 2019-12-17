<?php

namespace Neat\Object;

use ReflectionProperty;

class Property
{
    /**
     * @var ReflectionProperty
     */
    protected $reflection;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * Property constructor
     *
     * @param ReflectionProperty $reflection
     * @param string             $type
     * @note Activates the reflection's accessible flag
     */
    public function __construct(ReflectionProperty $reflection, string $type = null)
    {
        $reflection->setAccessible(true);

        $this->reflection = $reflection;
        $this->type       = $type;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function name(): string
    {
        return $this->reflection->getName();
    }

    /**
     * Is static?
     *
     * @return bool
     */
    public function static(): bool
    {
        return $this->reflection->isStatic();
    }

    /**
     * Get doc comment
     *
     * @return string
     */
    public function comment(): string
    {
        return $this->reflection->getDocComment() ?: '';
    }

    /**
     * Get type
     *
     * @return string|null
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * Cast value to string
     *
     * @param mixed $value
     * @return string
     */
    public function toString($value): string
    {
        return (string)$value;
    }

    /**
     * Cast value from string
     *
     * @param string $value
     * @return mixed
     */
    public function fromString(string $value)
    {
        return $value;
    }

    /**
     * Get Value
     *
     * @param object $object
     * @return mixed
     */
    public function get($object)
    {
        if (!$this->reflection->isInitialized($object)) {
            return null;
        }
        $value = $this->reflection->getValue($object);
        if ($value === null) {
            return null;
        }

        return $this->toString($value);
    }

    /**
     * Set value
     *
     * @param object $object
     * @param mixed  $value
     */
    public function set($object, $value)
    {
        if ($value !== null) {
            $value = $this->fromString($value);
        }

        $this->reflection->setValue($object, $value);
    }
}
