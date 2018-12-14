<?php

namespace Neat\Object\Decorator;

class UpdateAt extends TimeStamp
{
    public function store($entity)
    {
        $this->property->set($entity, 'now');
        parent::store($entity);
    }
}
