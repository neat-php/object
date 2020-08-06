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
     * @param \DateTimeImmutable $value
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
    public function fromString(string $value): ?\DateTimeImmutable
    {
        if ($value === '0000-00-00 00:00:00' || $value === '0000-00-00') {
            return null;
        }

        return new \DateTimeImmutable($value);
    }
}
