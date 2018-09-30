<?php

namespace Neat\Object;

use Neat\Object\Relations\Many;
use Neat\Object\Relations\One;
use Neat\Object\Relations\Reference\JunctionTable;
use Neat\Object\Relations\Reference\LocalKey;
use Neat\Object\Relations\Reference\RemoteKey;

trait Relations
{
    /**
     * @nostorage
     * @var Cache
     */
    private $relations;

    /**
     * @return Relation[]
     */
    public function relations(): array
    {
        return $this->relations->all();
    }

    protected function remoteKey($remote): RemoteKey
    {
        $local            = get_class($this);
        $policy           = $this->manager()->policy();
        $localKey         = $policy->key($local);
        $foreignKey       = $policy->foreignKey($local);
        $localProperties  = $policy->properties($local);
        $remoteProperties = $policy->properties($remote);

        return new RemoteKey(
            $localProperties[reset($localKey)],
            $remoteProperties[$foreignKey],
            $foreignKey,
            $this->manager()->repository($remote)
        );
    }

    public function hasOne(string $class): One
    {
        /** @var One $relation */
        $relation = $this->relations->get(__METHOD__ . $class, function () use ($class) {
            return new One($this->remoteKey($class), $this);
        });

        return $relation;
    }

    public function hasMany(string $class): Many
    {
        /** @var Many $relation */
        $relation = $this->relations->get(__METHOD__ . $class, function () use ($class) {
            new Many($this->remoteKey($class), $this);
        });

        return $relation;
    }

    protected function localKey(string $remote): LocalKey
    {
        $local            = get_class($this);
        $policy           = $this->manager()->policy();
        $localForeignKey  = $policy->foreignKey($remote);
        $remoteKey        = $policy->key($remote);
        $localProperties  = $policy->properties($local);
        $remoteProperties = $policy->properties($remote);

        return new LocalKey(
            $localProperties[$localForeignKey],
            $remoteProperties[reset($remoteKey)],
            reset($remoteKey),
            $this->manager()->repository($remote)
        );
    }

    public function belongsToOne(string $class): One
    {
        /** @var One $relation */
        $relation = $this->relations->get("belongsToOne$class", function () use ($class) {
            return new One($this->localKey($class), $this);
        });

        return $relation;
    }

    protected function junctionTable(string $remote): JunctionTable
    {
        $local            = get_class($this);
        $policy           = $this->manager()->policy();
        $localKey         = $policy->key($local);
        $remoteKey        = $policy->key($remote);
        $localForeignKey  = $policy->foreignKey($remote);
        $remoteForeignKey = $policy->foreignKey($local);
        $localProperties  = $policy->properties($local);
        $remoteProperties = $policy->properties($remote);

        return new JunctionTable(
            $localProperties[reset($localKey)],
            $remoteProperties[reset($remoteKey)],
            reset($remoteKey),
            $this->manager()->repository($remote),
            $this->manager()->connection(),
            $policy->junctionTable($local, $remote),
            $localForeignKey,
            $remoteForeignKey
        );
    }

    public function belongsToMany(string $class): Many
    {
        /** @var Many $relation */
        $relation = $this->relations->get(__METHOD__ . $class, function () use ($class) {
            return new Many($this->junctionTable($class), $this);
        });

        return $relation;
    }

    public abstract function manager(): Manager;
}
