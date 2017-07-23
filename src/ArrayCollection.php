<?php

namespace Mix\Essential;

use ArrayAccess;

class ArrayCollection implements ArrayAccess
{
    protected $items = [];

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