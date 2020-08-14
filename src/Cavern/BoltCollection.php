<?php

namespace Cavern;

use ArrayIterator;
use IteratorAggregate;

class BoltCollection implements IteratorAggregate
{
    /** @var Bolt[] */
    private array $bolts = [];

    public function reset(): void
    {
        unset($this->bolts);
        $this->bolts = [];
    }

    public function add(Bolt $bolt): void
    {
        $this->bolts[] = $bolt;
    }

    public function removeNotActive(): void
    {
        $this->bolts = array_filter($this->bolts, fn($bolt) => $bolt->isActive());
    }

    public function getIterator()
    {
        return new ArrayIterator($this->bolts);
    }
}
