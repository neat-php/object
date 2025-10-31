<?php

namespace Neat\Object;

use ReflectionProperty;

class Property
{
    /** @var ReflectionProperty */
    protected $reflection;

    /**
     * @note Activates the reflection's accessible flag
     */
    public function __construct(ReflectionProperty $reflection)
    {
        $reflection->setAccessible(true);

        $this->reflection = $reflection;
    }

    public function name(): string
    {
        return $this->reflection->getName();
    }

    public function static(): bool
    {
        return $this->reflection->isStatic();
    }

    /**
     * Get the doc comment
     */
    public function comment(): string
    {
        return $this->reflection->getDocComment() ?: '';
    }

    /**
     * Cast value to string
     *
     * @param mixed $value
     */
    public function toString($value): string
    {
        return (string) $value;
    }

    /**
     * Cast value from string
     *
     * @return mixed
     */
    public function fromString(string $value)
    {
        return $value;
    }

    public function isInitialized(object $object): bool
    {
        if (PHP_VERSION_ID >= 70400) {
            return $this->reflection->isInitialized($object);
        }
        $value = $this->reflection->getValue($object);

        return $value !== null;
    }

    /**
     * Get the value from the given object
     */
    public function get(object $object): ?string
    {
        $value = $this->reflection->getValue($object);
        if ($value === null) {
            return null;
        }

        return $this->toString($value);
    }

    /**
     * Set the given value on the given object
     *
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
