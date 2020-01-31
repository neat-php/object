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
    private Cache $relations;

    public function relations(): Cache
    {
        if (!isset($this->relations)) {
            $this->relations = new Cache();
        }

        return $this->relations;
    }

    public function hasOne(string $class): One
    {
        /** @var One $relation */
        $relation = $this->relations()
            ->get(__METHOD__ . $class, function () use ($class) {
                return new One($this->manager()->remoteKey(static::class, $class), $this);
            });

        return $relation;
    }

    public function hasMany(string $class): Many
    {
        /** @var Many $relation */
        $relation = $this->relations()
            ->get(__METHOD__ . $class, function () use ($class) {
                return new Many($this->manager()->remoteKey(static::class, $class), $this);
            });

        return $relation;
    }

    public function belongsToOne(string $class): One
    {
        /** @var One $relation */
        $relation = $this->relations()
            ->get(__METHOD__ . $class, function () use ($class) {
                return new One($this->manager()->localKey(static::class, $class), $this);
            });

        return $relation;
    }

    public function belongsToMany(string $class): Many
    {
        /** @var Many $relation */
        $relation = $this->relations()
            ->get(__METHOD__ . $class, function () use ($class) {
                return new Many($this->manager()->junctionTable(static::class, $class), $this);
            });

        return $relation;
    }

    abstract public static function manager(): Manager;
}
