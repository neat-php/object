<?php

namespace Neat\Object\Property;

use Neat\Object\Property;

class FloatProperty extends Property
{
    public function fromString(string $value): float
    {
        return (float) $value;
    }
}
