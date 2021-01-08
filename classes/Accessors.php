<?php

namespace Neat\Object;

use Error;

trait Accessors
{
    /**
     * @param string $method
     * @param array  $arguments
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        if (preg_match('/^(add|all|has|get|remove|select|set)(.*)$/i', $method, $matches) &&
            method_exists($this, $relation = $this::manager()->policy()->accessorRelationMethod($matches[1], $matches[2])) &&
            method_exists($instance = $this->$relation(), $operation = strtolower($matches[1]))
        ) {
            return $instance->$operation(...$arguments);
        }

        throw new Error('Call to undefined method ' . static::class . '::' . $method . '()');
    }
}
