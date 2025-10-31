<?php

namespace Neat\Object\Property;

use DateTimeInterface;
use Neat\Object\Property;

class DateTimeImmutable extends Property
{
    /**
     * Cast DateTimeImmutable to string
     *
     * @param mixed $value
     */
    public function toString($value): string
    {
        if (!$value instanceof DateTimeInterface) {
            $value = new \DateTimeImmutable($value);
        }

        return $value->format('Y-m-d H:i:s');
    }

    /**
     * Cast value from scalar to a DateTimeImmutable
     */
    public function fromString(string $value): \DateTimeImmutable
    {
        return new \DateTimeImmutable($value);
    }
}
