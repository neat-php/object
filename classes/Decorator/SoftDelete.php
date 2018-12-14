<?php

namespace Neat\Object\Decorator;

use Neat\Object\Collection;
use Traversable;

class SoftDelete extends TimeStamp
{
    public function all($conditions = null): array
    {
        return parent::all($this->query($conditions)->where([$this->column, new \DateTime]));
    }

    public function collection($conditions = null): Collection
    {
        return parent::collection($this->query($conditions)->where([$this->column, new \DateTime]));
    }

    public function iterate($conditions = null): Traversable
    {
        return parent::iterate($this->query($conditions)->where([$this->column, new \DateTime]));
    }

    /**
     * @param object $entity
     * @return false|int
     */
    public function delete($entity)
    {
        $this->property->set($entity, 'now');
        $this->store($entity);

        return 1;
    }
}
