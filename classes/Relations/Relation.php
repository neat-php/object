<?php

namespace Neat\Object\Relations;

use Neat\Object\Relations\Reference\LocalKey;

/**
 * @template TLocal of object
 * @template TRemote of object
 */
abstract class Relation
{
    /** @var Reference<TLocal, TRemote> */
    protected $reference;

    /** @var TLocal */
    protected $local;

    /** @var bool */
    protected $loaded = false;

    /** @var array<TRemote> */
    protected $objects = [];

    /**
     * Relation constructor.
     * @param Reference<TLocal, TRemote> $reference
     * @param TLocal                     $local
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

    /**
     * @internal
     */
    public function setRelation(): void
    {
        if ($this instanceof One && $this->reference instanceof LocalKey) {
            $this->store();
        }
    }

    /**
     * @internal
     */
    public function storeRelation(): void
    {
        if (!$this instanceof One || !$this->reference instanceof LocalKey) {
            $this->store();
        }
    }
}
