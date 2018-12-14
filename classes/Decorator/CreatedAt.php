<?php

namespace Neat\Object\Decorator;

class CreatedAt extends TimeStamp
{
    public function store($entity)
    {
        if (!$this->property->get($entity)) {
            $this->property->set($entity, 'now');
        }

        parent::store($entity);
    }
}
