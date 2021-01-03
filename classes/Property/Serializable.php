<?php

/** @noinspection PhpElementIsNotAvailableInCurrentPhpVersionInspection */
/** @noinspection PhpLanguageLevelInspection */

namespace Neat\Object\Property;

use Neat\Object\Property;
use ReflectionClass;
use ReflectionProperty;

class Serializable extends Property
{
    protected ReflectionClass $factory;

    public function __construct(ReflectionProperty $reflection)
    {
        parent::__construct($reflection);
        $type = $reflection->getType();
        if ($type instanceof \ReflectionNamedType) {
            $this->factory = new ReflectionClass($type->getName());

            return;
        }

        throw new \TypeError();
    }

    /**
     * Cast Serializable to string
     *
     * @param \Serializable $value
     * @return string
     */
    public function toString($value): string
    {
        if ($value instanceof \Serializable) {
            return $value->serialize();
        }

        throw new \TypeError();
    }

    /**
     * Cast Serializable from string
     *
     * @param string $value
     * @return \Serializable|object
     */
    public function fromString(string $value)
    {
        /** @var \Serializable $object */
        $object = $this->factory->newInstanceWithoutConstructor();
        $object->unserialize($value);

        return $object;
    }
}
