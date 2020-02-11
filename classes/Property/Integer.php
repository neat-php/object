<?php

namespace Neat\Object\Property;

use Neat\Object\Property;

class Integer extends Property
{
    /**
     * Cast value from bool to scalar
     *
     * @param int $value
     * @return string
     */
    public function toString($value): string
    {
        return (string) $value;
    }

    /**
     * Cast value from scalar to type
     *
     * @param string $value
     * @return int
     */
    public function fromString(string $value): int
    {
        return (int) $value;
    }
}
