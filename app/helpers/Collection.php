<?php

namespace Helpers;

class Collection {

    private array $items;

    public function __construct(array $init = []) {
        $this->items = $init;
    }

    public function add($item) {
        $this->items[] = $item;
    }

    public function toArray() : array {
        $result = [];
        if (count($this->items))
            foreach ($this->items as $item)
                $result[] = $item->toArray();
        return $result;
    }

    public function count() : int {
        return count($this->items);
    }

    public function remove(int $index) {
        if ($index < $this->count())
            unset($this->items[$index]); 
    }

    public function shift()
    {
        return array_shift($this->items);
    }

    public function __toString() : string
    {
        return json_encode($this->items);
    }
}