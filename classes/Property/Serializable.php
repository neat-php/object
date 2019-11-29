<?php

namespace Neat\Object\Property;

use Neat\Object\Property;
use ReflectionClass;
use ReflectionProperty;

class Serializable extends Property
{
    protected ReflectionClass $factory;

    /**
     * Property constructor
     *
     * @param ReflectionProperty $reflection
     * @param string             $type
     */
    public function __construct(ReflectionProperty $reflection, string $type = null)
    {
        parent::__construct($reflection, $type);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->factory = new ReflectionClass($type);
    }

    /**
     * Cast Serializable to string
     *
     * @param Serializable|object $value
     * @return string
     */
    public function toString($value): string
    {
        if ($value instanceof \Serializable) {
            return $value->serialize();
        }

        return $value;
    }

    /**
     * Cast Serializable from string
     *
     * @param string $value
     * @return Serializable|object
     */
    public function fromString(string $value)
    {
        /** @var \Serializable $object */
        $object = $this->factory->newInstanceWithoutConstructor();
        $object->unserialize($value);

        return $object;
    }}
