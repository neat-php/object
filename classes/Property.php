<?php

namespace Neat\Object;

use ReflectionProperty;

class Property
{
    protected ReflectionProperty $reflection;

    /**
     * Property constructor
     *
     * @param ReflectionProperty $reflection
     * @note Activates the reflection's accessible flag
     */
    public function __construct(ReflectionProperty $reflection)
    {
        $reflection->setAccessible(true);

        $this->reflection = $reflection;
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
        if ($this->reflection->getDocComment()) {
            return $this->reflection->getDocComment();
        }

        return '';
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
     * @return string|null
     */
    public function get(object $object): ?string
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
    public function set(object $object, $value): void
    {
        if ($value !== null) {
            $value = $this->fromString($value);
        }

        $this->reflection->setValue($object, $value);
    }
}
