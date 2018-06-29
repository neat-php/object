<?php

namespace Neat\Object;

class Collection implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @var string
     */
    protected $type;

    /**
     * ArrayCollection constructor.
     * @param mixed[] $items
     * @param string|null $type
     */
    public function __construct(array $items, string $type = null)
    {
        $this->items = $items;
        if ($type) {
            $this->type = $type;
        } elseif (($item = reset($items)) && is_object($item)) {
            $this->type = get_class($item);
        }
    }

    /**
     * @param mixed|object $item
     * @param string $method
     * @throws \TypeError
     */
    protected function validate($item, $method)
    {
        if ($this->type && !$item instanceof $this->type) {
            $class = get_class($item);
            throw new \TypeError("Argument 1 passed to $method must be of the type {$this->type}, $class given");
        }
    }

    /**
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->validate($value, __METHOD__);
        $this->items[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Returns the first item in the array
     *
     * @return mixed
     */
    public function first()
    {
        return reset($this->items);
    }

    /**
     * @param mixed $item
     * @return $this
     */
    public function push($item)
    {
        $this->validate($item, __METHOD__);
        $this[] = $item;

        return $this;
    }

    /**
     * Return the copies filtered by the given callback
     * The callback should accept an `$item` parameter
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback)
    {
        return new static(array_filter($this->items, $callback));
    }

    /**
     * Maps the internal array over the given callback
     * The callback should accept an `$item` parameter
     *
     * @param callable $callback
     * @param null|string $arrayCollection
     * @return array
     */
    public function map(callable $callback, $arrayCollection = null)
    {
        if ($arrayCollection) {
            return new $arrayCollection($this->map($callback));
        }

        return array_map($callback, $this->items);
    }

    /**
     * Return the given column for every item
     *
     * @param string $column
     * @param null|string $arrayCollection
     * @return array
     */
    public function column($column, $arrayCollection = null)
    {
        if ($arrayCollection) {
            return new $arrayCollection($this->column($column));
        }

        return array_column($this->items, $column);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->items;
    }
}
