<?php

namespace Neat\Object;

class Cache
{
    /** @var object[] */
    protected $objects = [];

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->objects[$key]);
    }

    /**
     * @param string   $key
     * @param callable $factory
     * @return object
     */
    public function get(string $key, callable $factory)
    {
        return $this->has($key) ? $this->objects[$key] : $this->set($key, $factory());
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->objects;
    }

    /**
     * @param string $key
     * @param object $object
     * @return object
     */
    public function set(string $key, $object)
    {
        return $this->objects[$key] = $object;
    }
}
