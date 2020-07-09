<?php

namespace Cavern;

use ArrayIterator;
use IteratorAggregate;

class FruitCollection implements IteratorAggregate
{
    /** @var Fruit[] */
    private array $fruits = [];

    public function reset(): void
    {
        unset($this->fruits);
        $this->fruits = [];
    }

    public function add(Fruit $fruit): void
    {
        $this->fruits[] = $fruit;
    }

    public function removeNotActive(): void
    {
        $this->fruits = array_filter($this->fruits, fn($fruit) => $fruit->isActive());
    }

    public function getIterator()
    {
        return new ArrayIterator($this->fruits);
    }
}
