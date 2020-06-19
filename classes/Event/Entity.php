<?php

namespace Neat\Object\Event;

trait Entity
{
    /** @var object */
    private $entity;

    /**
     * Event constructor
     *
     * @param object $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return object
     */
    public function entity()
    {
        return $this->entity;
    }
}
