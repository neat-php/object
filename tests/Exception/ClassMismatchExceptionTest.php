<?php

namespace Neat\Object\Test\Exception;

use Neat\Object\Exception\ClassMismatchException;
use Neat\Object\Reference\LocalKey;
use Neat\Object\Reference\RemoteKey;
use PHPUnit\Framework\TestCase;

class ClassMismatchExceptionTest extends TestCase
{
    public function testCreate()
    {
        $this->assertSame(
            "Expected instanceof '" . LocalKey::class . "', got '" . RemoteKey::class . "'",
            (new ClassMismatchException(LocalKey::class, RemoteKey::class))->getMessage()
        );
    }
}
