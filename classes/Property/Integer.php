<?php

namespace Neat\Object\Property;

use Neat\Object\Property;

class Integer extends Property
{
    public function fromString(string $value): int
    {
        return (int) $value;
    }
}
