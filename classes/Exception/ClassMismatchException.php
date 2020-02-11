<?php

namespace Neat\Object\Exception;

use LogicException;

class ClassMismatchException extends LogicException
{
    public function __construct(string $expected, string $actual)
    {
        parent::__construct("Expected instanceof '$expected', got '$actual'");
    }
}
