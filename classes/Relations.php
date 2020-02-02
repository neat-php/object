<?php

namespace Neat\Object;

use Neat\Object\Relations\Many;
use Neat\Object\Relations\One;
use Neat\Object\Relations\RelationBuilder;

trait Relations
{
    /**
     * @nostorage
     * @var Cache
     */
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

    public function buildHasOne(string $key, string $remoteClass): RelationBuilder
    {
        /** @var RelationBuilder $relationBuilder */
        $relationBuilder = $this->relations()->get(
            $key,
            function () use ($key, $remoteClass): RelationBuilder {
                $localClass = get_class($this);
                $builder    = $this->manager()->buildRemoteKey($key, $localClass, $remoteClass);

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
     * @param string $remoteClass
     * @return One
     */
    public function hasOne(string $remoteClass): One
    {
        /** @var One $relation */
        $relation = $this->buildHasOne(__METHOD__ . $remoteClass, $remoteClass)->resolve();

        return $relation;
    }

    public function buildHasMany(string $key, string $remoteClass): RelationBuilder
    {
        /** @var RelationBuilder $relationBuilder */
        $relationBuilder = $this->relations()->get(
            $key,
            function () use ($key, $remoteClass): RelationBuilder {
                $localClass = get_class($this);
                $builder    = $this->manager()->buildRemoteKey($key, $localClass, $remoteClass);

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
     * @param string $remoteClass
     * @return Many
     */
    public function hasMany(string $remoteClass): Many
    {
        /** @var Many $relation */
        $relation = $this->buildHasMany(__METHOD__ . $remoteClass, $remoteClass)->resolve();

        return $relation;
    }

    public function buildBelongsToOne(string $remoteClass, string $key): RelationBuilder
    {
        /** @var RelationBuilder $relationBuilder */
        $relationBuilder = $this->relations()->get(
            $key,
            function () use ($key, $remoteClass): RelationBuilder {
                $localClass = get_class($this);
                $builder    = $this->manager()->buildLocalKey($key, $localClass, $remoteClass);

                return new RelationBuilder(One::class, $builder, $this);
            }
        );

        return $relationBuilder;
    }

    /**
     * @param string $class
     * @return One
     */
    public function belongsToOne(string $class): One
    {
        /** @var One $relation */
        $relation = $this->buildBelongsToOne($class, __METHOD__ . $class)->resolve();

        return $relation;
    }

    public function buildBelongsToMany(string $key, string $remoteClass): RelationBuilder
    {
        /** @var RelationBuilder $relationBuilder */
        $relationBuilder = $this->relations()->get(
            $key,
            function () use ($key, $remoteClass): RelationBuilder {
                $localClass = get_class($this);
                $builder    = $this->manager()->buildJunctionTable($key, $localClass, $remoteClass);

                return new RelationBuilder(Many::class, $builder, $this);
            }
        );

        return $relationBuilder;
    }

    /**
     * @param string $remoteClass
     * @return Many
     */
    public function belongsToMany(string $remoteClass): Many
    {
        /** @var Many $relation */
        $relation = $this->buildBelongsToMany(__METHOD__ . $remoteClass, $remoteClass)->resolve();

        return $relation;
    }

    abstract public static function manager(): Manager;
}
