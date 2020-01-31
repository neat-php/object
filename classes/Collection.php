<?php

namespace Neat\Object;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;

class Collection implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
    use Collectible;

    protected array $items;

    /**
     * Collection constructor
     *
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    protected function &items(): array
    {
        return $this->items;
    }
}
