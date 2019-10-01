<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

namespace Neat\Object\Property;

use Neat\Object\Property;

class DateTime extends Property
{
    /**
     * Cast value from bool to scalar
     *
     * @param \DateTime $value
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
     * @return \DateTime
     */
    public function fromString(string $value): \DateTime
    {
        return new \DateTime($value);
    }
}
