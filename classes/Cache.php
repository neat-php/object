<?php

namespace Neat\Object;

class Cache
{
    /**
     * @var object[]
     */
    protected array $objects = [];

    public function has(string $key): bool
    {
        return isset($this->objects[$key]);
    }

    public function get(string $key, callable $factory)
    {
        return $this->has($key) ? $this->objects[$key] : $this->set($key, $factory());
    }

    /**
     * @return object[]
     */
    public function all(): array
    {
        return $this->objects;
    }

    public function set(string $key, object $object): object
    {
        return $this->objects[$key] = $object;
    }
}
