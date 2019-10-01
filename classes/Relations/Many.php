<?php

namespace Neat\Object\Relations;

use Neat\Object\Collectible;
use Neat\Object\Query;

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
        if (!$this->has($remote)) {
            $this->objects[] = $remote;
        }

        return $this;
    }

    /**
     * @param object $remote
     * @return Many
     */
    public function remove($remote): self
    {
        $this->all();
        foreach ($this->objects as $index => $object) {
            if ($this->reference->getRemoteKeyValue($object) == $this->reference->getRemoteKeyValue($remote)) {
                unset($this->objects[$index]);

                return $this;
            }
        }

        return $this;
    }

    /**
     * @param $remote
     * @return bool
     */
    public function has($remote): bool
    {
        $remoteKeyValue = $this->reference->getRemoteKeyValue($remote);
        if ($remoteKeyValue === null) {
            return false;
        }

        foreach ($this->all() as $object) {
            if ($this->reference->getRemoteKeyValue($object) == $remoteKeyValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Query
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
