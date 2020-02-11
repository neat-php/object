<?php

namespace Neat\Object\Exception;

use LogicException;

class PropertyNotFoundException extends LogicException
{
    public function __construct(string $class, string $property)
    {
        parent::__construct("Class: '$class' doesn't have a property $property");
    }
}
