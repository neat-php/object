<?php

namespace Neat\Object;

/**
 * Class Relation
 * @package Neat\Object
 */
class Relation
{
    private $owner;

    /**
     * @var Entity|ArrayCollection
     */
    private $related;

    public static function belongsToOne()
    {
        
    }

    public static function belongsToMany(Entity $related, string $owner)
    {
        
    }

    public static function hasOne(Entity $owner, string $related)
    {
        
    }

    public static function hasMany(Entity $owner, string $related, $collection = ArrayCollection::class)
    {

    }
}
