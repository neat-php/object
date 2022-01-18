<?php

namespace Neat\Object;

use Neat\Object\Relations\Many;
use Neat\Object\Relations\One;
use Neat\Object\Relations\Reference\JunctionTableBuilder;
use Neat\Object\Relations\Reference\LocalKeyBuilder;
use Neat\Object\Relations\Reference\RemoteKeyBuilder;
use Neat\Object\Relations\RelationBuilder;

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
        if (!$this->relations) {
            $this->relations = new Cache();
        }

        return $this->relations;
    }

    /**
     * @template T
     * @param class-string<T>                      $remoteClass
     * @param string                               $key
     * @param null|callable(RemoteKeyBuilder):void $configure
     * @return RelationBuilder
     * @deprecated use hasOne() with $role and $configure parameter instead
     */
    public function buildHasOne(string $remoteClass, string $key, callable $configure = null): RelationBuilder
    {
        /** @var RelationBuilder $relationBuilder */
        $relationBuilder = $this->relations()->get(
            $key,
            function () use ($key, $remoteClass, $configure): RelationBuilder {
                $localClass = get_class($this);
                $builder    = $this->manager()->buildRemoteKey($key, $localClass, $remoteClass, $configure);

                return new RelationBuilder(One::class, $builder, $this);
            }
        );

        return $relationBuilder;
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
     * @template T
     * @param class-string<T>                      $remoteClass
     * @param string|null                          $role
     * @param null|callable(RemoteKeyBuilder):void $configure
     * @return One<static, T>
     */
    public function hasOne(string $remoteClass, string $role = null, callable $configure = null): One
    {
        $key = get_class($this) . ($role ?? __FUNCTION__) . $remoteClass;

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpDeprecationInspection */
        return $this->buildHasOne($remoteClass, $key, $configure)->resolve();
    }

    /**
     * @template T
     * @param class-string<T>                      $remoteClass
     * @param string                               $key
     * @param null|callable(RemoteKeyBuilder):void $configure
     * @return RelationBuilder
     * @deprecated use hasMany() with $role and $configure parameter instead
     */
    public function buildHasMany(string $remoteClass, string $key, callable $configure = null): RelationBuilder
    {
        /** @var RelationBuilder $relationBuilder */
        $relationBuilder = $this->relations()->get(
            $key,
            function () use ($key, $remoteClass, $configure): RelationBuilder {
                $localClass = get_class($this);
                $builder    = $this->manager()->buildRemoteKey($key, $localClass, $remoteClass, $configure);

                return new RelationBuilder(Many::class, $builder, $this);
            }
        );

        return $relationBuilder;
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
     * @template T
     * @param class-string<T>                      $remoteClass
     * @param string|null                          $role
     * @param null|callable(RemoteKeyBuilder):void $configure
     * @return Many<static, T>
     */
    public function hasMany(string $remoteClass, string $role = null, callable $configure = null): Many
    {
        $key = get_class($this) . ($role ?? __FUNCTION__) . $remoteClass;

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpDeprecationInspection */
        return $this->buildHasMany($remoteClass, $key, $configure)->resolve();
    }

    /**
     * @template T
     * @param class-string<T>                     $remoteClass
     * @param string                              $key
     * @param null|callable(LocalKeyBuilder):void $configure
     * @return RelationBuilder
     * @deprecated use belongsToOne() with $role and $configure parameter instead
     */
    public function buildBelongsToOne(string $remoteClass, string $key, callable $configure = null): RelationBuilder
    {
        /** @var RelationBuilder $relationBuilder */
        $relationBuilder = $this->relations()->get(
            $key,
            function () use ($key, $remoteClass, $configure): RelationBuilder {
                $localClass = get_class($this);
                $builder    = $this->manager()->buildLocalKey($key, $localClass, $remoteClass, $configure);

                return new RelationBuilder(One::class, $builder, $this);
            }
        );

        return $relationBuilder;
    }

    /**
     * @template T
     * @param class-string<T>                     $remoteClass
     * @param string|null                         $role
     * @param null|callable(LocalKeyBuilder):void $configure
     * @return One<static, T>
     */
    public function belongsToOne(string $remoteClass, string $role = null, callable $configure = null): One
    {
        $key = get_class($this) . ($role ?? __FUNCTION__) . $remoteClass;

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpDeprecationInspection */
        return $this->buildBelongsToOne($remoteClass, $key, $configure)->resolve();
    }

    /**
     * @template T
     * @param class-string<T>                          $remoteClass
     * @param string                                   $key
     * @param null|callable(JunctionTableBuilder):void $configure
     * @return RelationBuilder
     * @deprecated use belongsToMany() with $role and $configure parameter instead
     */
    public function buildBelongsToMany(string $remoteClass, string $key, callable $configure = null): RelationBuilder
    {
        /** @var RelationBuilder $relationBuilder */
        $relationBuilder = $this->relations()->get(
            $key,
            function () use ($key, $remoteClass, $configure): RelationBuilder {
                $localClass = get_class($this);
                $builder    = $this->manager()->buildJunctionTable($key, $localClass, $remoteClass, $configure);

                return new RelationBuilder(Many::class, $builder, $this);
            }
        );

        return $relationBuilder;
    }

    /**
     * @template T
     * @param class-string<T>                          $remoteClass
     * @param string|null                              $role
     * @param null|callable(JunctionTableBuilder):void $configure
     * @return Many<static, T>
     */
    public function belongsToMany(string $remoteClass, string $role = null, callable $configure = null): Many
    {
        $key = get_class($this) . ($role ?? __FUNCTION__) . $remoteClass;

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpDeprecationInspection */
        return $this->buildBelongsToMany($remoteClass, $key, $configure)->resolve();
    }

    abstract public static function manager(): Manager;
}
