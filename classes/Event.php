<?php

namespace Neat\Object;

abstract class Event
{
    public const STORING  = 'storing';
    public const STORED   = 'stored';
    public const LOADING  = 'loading';
    public const LOADED   = 'loaded';
    public const DELETING = 'deleting';
    public const DELETED  = 'deleted';

    /** @var object */
    private $entity;

    /**
     * Event constructor
     *
     * @param object $entity
     */
    public function __construct(object $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return object
     */
    public function entity(): object
    {
        return $this->entity;
    }
}
