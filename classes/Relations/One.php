<?php

namespace Neat\Object\Relations;

use Neat\Object\Query;

class One extends Relation
{
    /**
     * @return object|null
     */
    public function get(): ?object
    {
        if (!$this->loaded()) {
            $this->load();
        }

        return reset($this->objects) ?: null;
    }

    /**
     * @param object|null $remote
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
     * @return Query
     */
    public function select(): Query
    {
        return $this->reference->select($this->local);
    }
}
