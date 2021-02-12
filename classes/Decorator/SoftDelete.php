<?php

namespace Neat\Object\Decorator;

use Neat\Database\SQLQuery;
use Neat\Object\Collection;
use Traversable;

class SoftDelete extends TimeStamp
{
    /**
     * @inheritDoc
     */
    public function one($conditions = null): ?object
    {
        if ($conditions instanceof SQLQuery) {
            return parent::one($conditions);
        }

        return parent::one($this->query($conditions)->where([$this->column => null]));
    }

    /**
     * @inheritDoc
     */
    public function all($conditions = null): array
    {
        if ($conditions instanceof SQLQuery) {
            return parent::all($conditions);
        }

        return parent::all($this->query($conditions)->where([$this->column => null]));
    }

    /**
     * @inheritDoc
     */
    public function collection($conditions = null): Collection
    {
        if ($conditions instanceof SQLQuery) {
            return parent::collection($conditions);
        }

        return parent::collection($this->query($conditions)->where([$this->column => null]));
    }

    /**
     * @inheritDoc
     */
    public function iterate($conditions = null): Traversable
    {
        if ($conditions instanceof SQLQuery) {
            return parent::iterate($conditions);
        }

        return parent::iterate($this->query($conditions)->where([$this->column => null]));
    }

    /**
     * @inheritDoc
     */
    public function delete(object $entity): int
    {
        if (!$this->property->get($entity)) {
            $this->property->set($entity, 'now');
            $this->store($entity);
        }

        return 1;
    }
}
