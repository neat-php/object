<?php

namespace Neat\Object\Relations;

use Neat\Object\Query;

class One extends Relation
{
    public function get(): ?object
    {
        if (!$this->loaded()) {
            $this->load();
        }

        if (reset($this->objects)) {
            return reset($this->objects);
        }

        return null;
    }

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

    public function select(): Query
    {
        return $this->reference->select($this->local);
    }
}
