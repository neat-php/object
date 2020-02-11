<?php

namespace Neat\Object\Relation;

use Neat\Object\Query;
use Neat\Object\Relation;

class One extends Relation
{
    /**
     * @return object|null
     */
    public function get()
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
    public function set($remote): self
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
    public function select()
    {
        return $this->reference->select($this->local);
    }
}
