<?php

namespace Mix\Essential;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

class ArrayCollection implements ArrayAccess, IteratorAggregate
{
    protected $items = [];

    public function __construct($items = [])
    {
        $this->items = $this->getArrayableItems($items);
    }

    public static function make($items = [])
    {
        return new static($items);
    }

    protected function getArrayableItems($items)
    {
        if (is_array($items)) { return $items; }
        elseif ($items instanceof self) { return $items->all(); }
        elseif ($items instanceof Traversable) { return iterator_to_array($items); }

        elseif ($items instanceof Arrayable) { return $items->toArray(); }
        elseif ($items instanceof Jsonable) { return json_decode($items->toJson(), true); }
        elseif ($items instanceof JsonSerializable) { return $items->jsonSerialize(); }

        return (array) $items;
    }

    /* IteratorAggregate Methods */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /* ArrayAccess Methods */
    public function offsetExists($offset) { return array_key_exists($offset, $this->items); }
    public function offsetGet($offset) { return $this->items[$offset]; }
    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->items[] = $value;
        }
        else
        {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset) { unset($this->items[$offset]); }
}