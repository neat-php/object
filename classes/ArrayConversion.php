<?php

namespace Neat\Object;

/**
 * Use ArrayConversion to use generic conversion from and to arrays
 */
trait ArrayConversion
{
    /**
     * Converts to an associative array
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        foreach (Property::list(static::class) as $key => $property) {
            $array[$key] = $property->get($this);
        }

        return $array;
    }

    /**
     * Converts from an associative array
     *
     * @param array $array
     */
    public function fromArray(array $array)
    {
        foreach (Property::list(static::class) as $key => $property) {
            $property->set($this, $array[$key] ?? null);
        }
    }
}
