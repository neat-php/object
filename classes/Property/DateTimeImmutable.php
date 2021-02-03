<?php

namespace Neat\Object\Property;

use DateTimeInterface;
use Exception;
use Neat\Object\Property;

class DateTimeImmutable extends Property
{
    /**
     * Cast DateTimeImmutable to string
     *
     * @param mixed $value
     * @return string
     * @throws Exception
     */
    public function toString($value): string
    {
        if (!$value instanceof DateTimeInterface) {
            $value = new \DateTimeImmutable($value);
        }

        return $value->format('Y-m-d H:i:s');
    }

    /**
     * Cast value from scalar to type
     *
     * @param string $value
     * @return \DateTimeImmutable
     * @throws Exception
     */
    public function fromString(string $value): \DateTimeImmutable
    {
        return new \DateTimeImmutable($value);
    }
}
