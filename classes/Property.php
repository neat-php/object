<?php

namespace Neat\Object;

use ReflectionProperty;

class Property
{
    /** @var ReflectionProperty */
    protected $reflection;

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
        return $this->reflection->getDocComment() ?: '';
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
     * @noinspection PhpMissingReturnTypeInspection
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
     * Get Value
     *
     * @param object $object
     * @return string|null
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
     * Set value
     *
     * @param object $object
     * @param mixed  $value
     * @return void
     */
    public function set(object $object, $value): void
    {
        if ($value !== null) {
            $value = $this->fromString($value);
        }

        $this->reflection->setValue($object, $value);
    }
}
