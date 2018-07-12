<?php

namespace Neat\Object;

use ReflectionClass;

class Policy
{
    /**
     * Get table name for class
     *
     * @param string $class
     * @return string
     */
    public function table(string $class): string
    {
        $path = explode('\\', $class);

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', array_pop($path)));
    }

    /**
     * Get column name for property
     *
     * @param Property $property
     * @return string
     */
    public function column(Property $property): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property->name()));
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * Get properties for class
     *
     * @param string $class
     * @return Property[]
     */
    public function properties(string $class)
    {
        $properties = [];
        /** @noinspection PhpUnhandledExceptionInspection */
        foreach ((new ReflectionClass($class))->getProperties() as $reflection) {
            $property = new Property($reflection);
            if ($this->skip($property)) {
                continue;
            }

            $properties[$this->column($property)] = $property;
        }

        return $properties;
    }

    /**
     * Skip property?
     *
     * @param Property $property
     * @return bool
     */
    public function skip(Property $property): bool
    {
        return $property->static() || preg_match('/\\s@nostorage\\s/', $property->docBlock());
    }

    /**
     * Get key property names for the given properties
     *
     * @param Property[] $properties
     * @return string[]
     */
    public function key(array $properties): array
    {
        $id  = null;
        $key = [];
        foreach ($properties as $property) {
            if (preg_match('/\\s@key\\s/', $property->docBlock())) {
                $key[] = $this->column($property);
                continue;
            }
            if ($property->name() === 'id') {
                $id = [$this->column($property)];
                continue;
            }
        }

        if (!empty($key)) {
            return $key;
        }

        if ($id) {
            return $id;
        }

        throw new \RuntimeException('Unable to determine the key');
    }
}
