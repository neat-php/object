<?php

namespace Neat\Object\Relations;

class One extends Relation
{
    /**
     * @return object|null
     */
    public function get()
    {
        if (!$this->loaded()) {
            $this->load($this->local);
        }

        return reset($this->objects) ?: null;
    }

    /**
     * @param object $remote
     * @return $this
     */
    public function set($remote): self
    {
        $this->loaded  = true;
        $this->objects = [$remote];

        return $this;
    }
}
