<?php

namespace Neat\Object\Relations;

abstract class Relation
{
    protected Reference $reference;

    protected object $local;

    protected bool $loaded = false;

    /**
     * @var object[]
     */
    protected array $objects;

    /**
     * Relation constructor.
     * @param Reference $reference
     * @param object    $local
     */
    public function __construct(Reference $reference, object $local)
    {
        $this->reference = $reference;
        $this->local     = $local;
    }

    public function loaded(): bool
    {
        return $this->loaded;
    }

    /**
     * @return $this
     */
    public function load(): self
    {
        $this->objects = $this->reference->load($this->local);
        $this->loaded  = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function store(): self
    {
        if ($this->loaded) {
            $this->reference->store($this->local, array_values($this->objects));
        }

        return $this;
    }
}
