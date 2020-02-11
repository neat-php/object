<?php

namespace Neat\Object\Exception;

class PropertyNotFoundException extends \LogicException
{
    /**
     * NonExistingProperty constructor.
     * @param string|object $class
     * @param string        $property
     */
    public function __construct($class, string $property)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        parent::__construct("Class: '$class' doesn't have a property $property");
    }
}
