<?php

namespace Neat\Object;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;

/**
 * @template T
 */
class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    /** @use Collectible<T> */
    use Collectible;

    /** @var array<T> */
    protected $items;

    /**
     * Collection constructor
     *
     * @param array<T> $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @inheritDoc
     */
    protected function &items(): array
    {
        return $this->items;
    }
}
