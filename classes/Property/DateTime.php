<?php

namespace Neat\Object\Property;

use DateTimeInterface;
use Exception;
use Neat\Object\Property;

class DateTime extends Property
{
    /**
     * Cast value from bool to scalar
     *
     * @param \DateTime $value
     * @return string
     * @throws Exception
     */
    public function toString($value): string
    {
        if (!$value instanceof DateTimeInterface) {
            $value = new \DateTime($value);
        }

        return $value->format('Y-m-d H:i:s');
    }

    /**
     * Cast value from scalar to type
     *
     * @param string $value
     * @return \DateTime
     * @throws Exception
     */
    public function fromString(string $value): ?\DateTime
    {
        if ($value === '0000-00-00 00:00:00' || $value === '0000-00-00') {
            return null;
        }

        return new \DateTime($value);
    }
}
