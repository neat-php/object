<?php

namespace Neat\Object\Property;

use DateTimeInterface;
use Neat\Object\Property;

class DateTime extends Property
{
    /**
     * Cast value from bool to scalar
     *
     * @param mixed $value
     */
    public function toString($value): string
    {
        if (!$value instanceof DateTimeInterface) {
            $value = new \DateTime($value);
        }

        return $value->format('Y-m-d H:i:s');
    }

    /**
     * Cast value from scalar to a DateTime
     */
    public function fromString(string $value): \DateTime
    {
        return new \DateTime($value);
    }
}
