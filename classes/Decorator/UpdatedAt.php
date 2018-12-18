<?php

namespace Neat\Object\Decorator;

class UpdatedAt extends TimeStamp
{
    public function store($entity)
    {
        $this->property->set($entity, 'now');

        parent::store($entity);
    }
}
