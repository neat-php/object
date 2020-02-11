<?php

namespace Neat\Object\Exception;

class ClassMismatchException extends \LogicException
{
    public function __construct(string $expected, string $actual)
    {
        parent::__construct("Expected instanceof '$expected', got '$actual'");
    }
}
