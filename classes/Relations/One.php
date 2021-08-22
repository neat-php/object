<?php

namespace Neat\Object\Relations;

use Neat\Object\Query;

/**
 * @template TLocal of object
 * @template TRemote of object
 * @extends Relation<TLocal, TRemote>
 */
class One extends Relation
{
    /**
     * @return TRemote|null
     */
    public function get(): ?object
    {
        if (!$this->loaded()) {
            $this->load();
        }

        return reset($this->objects) ?: null;
    }

    /**
     * @param TRemote|null $remote
     * @return $this
     */
    public function set(?object $remote): self
    {
        $this->loaded = true;
        if ($remote) {
            $this->objects = [$remote];
        } else {
            $this->objects = [];
        }

        return $this;
    }

    /**
     * @return Query<TRemote>
     */
    public function select(): Query
    {
        return $this->reference->select($this->local);
    }
}
