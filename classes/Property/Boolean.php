<?php

namespace Neat\Object\Property;

class Boolean extends Property
{
    /**
     * Cast boolean to string
     *
     * @param bool $value
     * @return string
     */
    public function toString($value): string
    {
        return $value ? '1' : '0';
    }

    /**
     * Cast boolean from string
     *
     * @param string $value
     * @return bool
     */
    public function fromString(string $value): bool
    {
        return (bool) $value;
    }
}
