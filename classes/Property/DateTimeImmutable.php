<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

namespace Neat\Object\Property;

use Neat\Object\Property;

class DateTimeImmutable extends Property
{
    /**
     * Cast DateTimeImmutable to string
     *
     * @param \DateTimeImmutable $value
     * @return string
     */
    public function toString($value): string
    {
        if ($value instanceof \DateTimeInterface) {
            $value = $value->format('Y-m-d H:i:s');
        }

        return $value;
    }

    /**
     * Cast value from scalar to type
     *
     * @param string $value
     * @return \DateTimeImmutable
     */
    public function fromString(string $value): \DateTimeImmutable
    {
        return new \DateTimeImmutable($value);
    }
}
