<?php

namespace Neat\Object\Test\Exception;

use Neat\Object\Exception\PropertyNotFoundException;
use Neat\Object\Test\Helper\User;
use PHPUnit\Framework\TestCase;

class NonExistingPropertyTest extends TestCase
{
    public function testCreate(): void
    {
        $this->assertSame(
            "Class: '" . User::class . "' doesn't have a property test",
            (new PropertyNotFoundException(User::class, 'test'))->getMessage()
        );
    }
}
