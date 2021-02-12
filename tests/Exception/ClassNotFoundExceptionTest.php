<?php

namespace Neat\Object\Test\Exception;

use Neat\Object\Exception\ClassNotFoundException;
use PHPUnit\Framework\TestCase;

class ClassNotFoundExceptionTest extends TestCase
{
    public function testCreate(): void
    {
        $this->assertSame(
            "Class: 'ThisIsANonExistingClass' not found!",
            (new ClassNotFoundException('ThisIsANonExistingClass'))->getMessage()
        );
    }
}
