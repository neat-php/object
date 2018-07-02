<?php

namespace Neat\Object;

class Policy
{
    /**
     * @param string $class
     * @return string
     */
    public function table(string $class): string
    {
        $path = explode('\\', $class);

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', array_pop($path)));
    }

    /**
     * @param Property $property
     * @return string
     */
    public function column(Property $property): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property->name()));
    }

    /**
     * @param Property $property
     * @return bool
     */
    public function skip(Property $property): bool
    {
        return $property->static() || preg_match('/\\s@nostorage\\s/', $property->docBlock());
    }

    /**
     * @param array $properties
     * @return string[]
     */
    public function key(array $properties): array
    {
        $id  = null;
        $key = [];
        /** @var Property $property */
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
