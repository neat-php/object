<?php

namespace Neat\Object\Decorator;

class UpdatedAt extends TimeStamp
{
    public function store(object $entity): void
    {
        $this->property->set($entity, 'now');

        parent::store($entity);
    }
}
