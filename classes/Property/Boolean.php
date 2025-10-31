<?php

namespace Neat\Object\Property;

use Neat\Object\Property;

class Boolean extends Property
{
    /**
     * Cast boolean to string
     *
     * @param mixed $value
     */
    public function toString($value): string
    {
        return $value ? '1' : '0';
    }

    public function fromString(string $value): bool
    {
        return (bool) $value;
    }
}
