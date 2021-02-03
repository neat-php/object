<?php

namespace Neat\Object;

use Neat\Object\Relations\Many;
use Neat\Object\Relations\One;

trait Relations
{
    /** @var Cache @nostorage */
    private $relations;

    /**
     * Caches the instantiated relations for an entity
     * Should be accessible for the repository to delegate the store method to the relations
     *
     * @return Cache
     */
    public function relations(): Cache
    {
        return $this->relations
            ?? $this->relations = new Cache();
    }

    /**
     * Returns a relation which expects the primary key of the local entity to match the foreign key of the referenced
     * remote. The foreign key should have a unique constraint to maintain integrity.
     *
     * When the foreign key is not allowed to be NULL, the consuming code should make sure that when changing the
     * remote, the old remote should be deleted or set to another value to prevent SQL exceptions upon storing.
     *
     * When store is called on the local entity it will be delegated to the relation. When the relation is not loaded no
     * action is executed. However when the relation is loaded and a remote entity is available the foreign key will be
     * set and the entity will be stored.
     *
     * @param class-string  $remoteClass
     * @param string|null   $role
     * @param callable|null $configure
     * @psalm-param callable(RemoteKeyBuilder)|null $configure
     * @return One
     */
    public function hasOne(string $remoteClass, string $role = null, callable $configure = null): One
    {
        $key = get_class($this) . ($role ?? __FUNCTION__) . $remoteClass;

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->relations()->get($key, function () use ($key, $remoteClass, $configure): One {
            $localClass = get_class($this);
            $reference  = $this->manager()->remoteKey($key, $localClass, $remoteClass, $configure);

            return new One($reference, $this);
        });
    }

    /**
     * Returns a relation which expects the primary key of the local entity to match the foreign key of the referenced
     * remotes.
     *
     * When the foreign key is not allowed to be NULL, the consuming code should make sure that when removing a remote,
     * the old remote should be deleted or set to another value to prevent SQL exceptions upon storing.
     *
     * When store is called on the local entity it will be delegated to the relation. When the relation is not loaded no
     * action is executed. However when the relation is loaded and remote entities are available the foreign key will be
     * set and the entities will be stored, updated and deleted when necessary.
     *
     * @param class-string  $remoteClass
     * @param string|null   $role
     * @param callable|null $configure
     * @psalm-param callable(RemoteKeyBuilder)|null $configure
     * @return Many
     */
    public function hasMany(string $remoteClass, string $role = null, callable $configure = null): Many
    {
        $key = get_class($this) . ($role ?? __FUNCTION__) . $remoteClass;

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->relations()->get($key, function () use ($key, $remoteClass, $configure): Many {
            $localClass = get_class($this);
            $reference  = $this->manager()->remoteKey($key, $localClass, $remoteClass, $configure);

            return new Many($reference, $this);
        });
    }

    /**
     * @param class-string  $remoteClass
     * @param string|null   $role
     * @param callable|null $configure
     * @psalm-param callable(LocalKeyBuilder)|null $configure
     * @return One
     */
    public function belongsToOne(string $remoteClass, string $role = null, callable $configure = null): One
    {
        $key = get_class($this) . ($role ?? __FUNCTION__) . $remoteClass;

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->relations()->get($key, function () use ($key, $remoteClass, $configure): One {
            $localClass = get_class($this);
            $reference  = $this->manager()->localKey($key, $localClass, $remoteClass, $configure);

            return new One($reference, $this);
        });
    }

    /**
     * @param class-string  $remoteClass
     * @param string|null   $role
     * @param callable|null $configure
     * @psalm-param callable(JunctionTableBuilder)|null $configure
     * @return Many
     */
    public function belongsToMany(string $remoteClass, string $role = null, callable $configure = null): Many
    {
        $key = get_class($this) . ($role ?? __FUNCTION__) . $remoteClass;

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->relations()->get($key, function () use ($key, $remoteClass, $configure): Many {
            $localClass = get_class($this);
            $reference  = $this->manager()->junctionTable($key, $localClass, $remoteClass, $configure);

            return new Many($reference, $this);
        });
    }

    abstract public static function manager(): Manager;
}
