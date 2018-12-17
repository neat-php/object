<?php

namespace Neat\Object\Relations;

use Neat\Object\Collectible;

class Many extends Relation
{
    use Collectible;

    /**
     * @param object[] $remotes
     * @return $this
     */
    public function set(array $remotes): self
    {
        $this->loaded  = true;
        $this->objects = $remotes;

        return $this;
    }

    /**
     * @param object $remote
     * @return $this
     */
    public function add($remote): self
    {
        $this->all();
        $this->objects[] = $remote;

        return $this;
    }

    /**
     * @param $remote
     * @return bool
     */
    public function has($remote): bool
    {
        foreach ($this->all() as $obj) {
            if ($this->reference->getRemoteKeyValue($obj) == $this->reference->getRemoteKeyValue($remote)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return \Neat\Object\Query
     */
    public function select()
    {
        return $this->reference->select($this->local);
    }


    /**
     * @return object[]
     */
    public function &items(): array
    {
        if (!$this->loaded()) {
            $this->load();
        }

        return $this->objects;
    }
}
