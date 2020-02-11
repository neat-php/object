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
     * @param array $data
     * @return $this
     * @see RepositoryInterface::fromArray()
     */
    public function fromArray(array $data): self
    {
        return $this::repository()->fromArray($this, $data);
    }

    /**
     * @return array
     * @see RepositoryInterface::toArray()
     */
    public function toArray(): array
    {
        return $this::repository()->toArray($this);
    }
}
