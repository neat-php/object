<?php

namespace Neat\Object\Repository;

class UpdatedAt extends TimeStamp
{
    public function store($entity)
    {
        $this->property->set($entity, 'now');

        parent::store($entity);
    }
}
