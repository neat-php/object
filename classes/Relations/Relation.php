<?php

namespace Neat\Object\Relations;

abstract class Relation
{
    /**
     * @var Reference
     */
    protected $reference;

    /**
     * @var object
     */
    protected $local;

    /**
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var object[]
     */
    protected $objects;

    /**
     * Relation constructor.
     * @param Reference $reference
     * @param object    $local
     */
    public function __construct(Reference $reference, $local)
    {
        $this->reference = $reference;
        $this->local     = $local;
    }

    public function loaded(): bool
    {
        return $this->loaded;
    }

    /**
     * @param object $local
     * @return $this
     */
    public function load($local): self
    {
        $this->objects = $this->reference->load($local);

        return $this;
    }

    /**
     * @return $this
     */
    public function store(): self
    {
        if ($this->loaded) {
            $this->reference->store($this->local, $this->objects);
        }

        return $this;
    }
}
