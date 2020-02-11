<?php

namespace Neat\Object;

trait ArrayConversion
{
    /**
     * Get repository
     *
     * @return Repository
     */
    abstract public static function repository(): Repository;

    /**
     * @param array $data
     * @return $this
     * @see Repository::fromArray()
     */
    public function fromArray(array $data): self
    {
        return $this::repository()->fromArray($this, $data);
    }

    /**
     * @return array
     * @see Repository::toArray()
     */
    public function toArray(): array
    {
        return $this::repository()->toArray($this);
    }
}
