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
     * @var Relation[]
     */
    private $relations;

    /**
     * @return Relation[]
     */
    public function relations(): array
    {
        return $this->relations;
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
        return new One($this->remoteKey($class), $this);
    }

    public function hasMany(string $class): Many
    {
        return new Many($this->remoteKey($class), $this);
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
        return new One($this->localKey($class), $this);
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

    public function belongsToMany(string $remote): Many
    {
        return new Many($this->junctionTable($remote), $this);
    }

    public abstract function manager(): Manager;
}
