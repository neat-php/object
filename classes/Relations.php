<?php

namespace Neat\Object;

use Neat\Object\Relations\Many;
use Neat\Object\Relations\One;

trait Relations
{
    /**
     * @nostorage
     * @var Cache
     */
    private $relations;

    /**
     * @return Cache
     */
    public function relations(): Cache
    {
        if (!$this->relations) {
            $this->relations = new Cache;
        }

        return $this->relations;
    }

    /**
     * @param string $class
     * @return One
     */
    public function hasOne(string $class): One
    {
        /** @var One $relation */
        $relation = $this->relations()
            ->get(__METHOD__ . $class, function () use ($class) {
                return new One($this->manager()->remoteKey(get_class($this), $class), $this);
            });

        return $relation;
    }

    /**
     * @param string $class
     * @return Many
     */
    public function hasMany(string $class): Many
    {
        /** @var Many $relation */
        $relation = $this->relations()
            ->get(__METHOD__ . $class, function () use ($class) {
                return new Many($this->manager()->remoteKey(get_class($this), $class), $this);
            });

        return $relation;
    }

    /**
     * @param string $class
     * @return One
     */
    public function belongsToOne(string $class): One
    {
        /** @var One $relation */
        $relation = $this->relations()
            ->get(__METHOD__ . $class, function () use ($class) {
                return new One($this->manager()->localKey(get_class($this), $class), $this);
            });

        return $relation;
    }

    /**
     * @param string $class
     * @return Many
     */
    public function belongsToMany(string $class): Many
    {
        /** @var Many $relation */
        $relation = $this->relations()
            ->get(__METHOD__ . $class, function () use ($class) {
                return new Many($this->manager()->junctionTable(get_class($this), $class), $this);
            });

        return $relation;
    }

    public abstract function manager(): Manager;
}
