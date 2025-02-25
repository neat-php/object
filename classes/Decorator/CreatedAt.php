<?php

namespace Neat\Object\Decorator;

class CreatedAt extends TimeStamp
{
    public function store(object $entity): void
    {
        if (!$this->property->isInitialized($entity) || !$this->property->get($entity)) {
            $this->property->set($entity, 'now');
        }

        parent::store($entity);
    }
}
