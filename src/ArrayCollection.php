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

    public static function wrap($value)
    {
        return $value instanceof self
            ? new static($value)
            : new static( !is_array($value)?[$value]:$value );
    }

    public static function unwrap($value)
    {
        return $value instanceof self ? $value->all() : $value;
    }

    public static function times($amount, callable $callback = null)
    {
        if ($amount < 1) { return new static; }
        if (is_null($callback)) { return new static(range(1, $amount)); }
        return (new static(range(1, $amount)))->map($callback);
    }

    public function map(callable $callback)
    {
        $keys = array_keys($this->items);
        $items = array_map($callback, $this->items, $keys);
        return new static(array_combine($keys, $items));
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