<?php

namespace Neat\Object;

trait ArrayConversion
{
    /**
     * Get repository
     *
     * @return RepositoryInterface
     */
    abstract public static function repository(): RepositoryInterface;

    /**
     * Convert from an associative array
     *
     * @param $array
     * @return mixed
     */
    public function fromArray($array)
    {
        return $this::repository()->fromArray($this, $array);
    }

    /**
     * Convert to an associative array
     *
     * @return array
     */
    public function toArray()
    {
        return $this::repository()->toArray($this);
    }
}
