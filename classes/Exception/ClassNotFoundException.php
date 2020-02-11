<?php

namespace Neat\Object\Exception;

class ClassNotFoundException extends \LogicException
{
    public function __construct(string $class)
    {
        parent::__construct("Class: '$class' not found!");
    }
}
