<?php

namespace Neat\Object\Relations;

use Neat\Object\Collectible;
use Neat\Object\Query;

/**
 * @template TLocal of object
 * @template TRemote of object
 * @extends Relation<TLocal, TRemote>
 */
class Many extends Relation
{
    /** @use Collectible<TRemote> */
    use Collectible;

    /**
     * @return array<TRemote>
     */
    public function get(): array
    {
        return $this->items();
    }

    /**
     * @param array<TRemote> $remotes
     * @return $this
     */
    public function set(array $remotes): self
    {
        $this->loaded  = true;
        $this->objects = $remotes;

        return $this;
    }

    /**
     * @param TRemote $remote
     * @return $this
     */
    public function add(object $remote): self
    {
        $this->all();
        if (!$this->has($remote)) {
            $this->objects[] = $remote;
        }

        return $this;
    }

    /**
     * @param TRemote $remote
     * @return $this
     */
    public function remove(object $remote): self
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
     * @param TRemote $remote
     * @return bool
     */
    public function has(object $remote): bool
    {
        $remoteKeyValue = $this->reference->getRemoteKeyValue($remote);
        foreach ($remoteKeyValue as $value) {
            if ($value === null) {
                return false;
            }
        }

        foreach ($this->all() as $object) {
            if ($this->reference->getRemoteKeyValue($object) == $remoteKeyValue) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Query<TRemote>
     */
    public function select(): Query
    {
        return $this->reference->select($this->local);
    }

    /**
     * @return array<TRemote>
     */
    public function &items(): array
    {
        if (!$this->loaded()) {
            $this->load();
        }

        return $this->objects;
    }
}
