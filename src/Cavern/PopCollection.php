<?php

namespace Cavern;

use ArrayIterator;
use IteratorAggregate;

class PopCollection implements IteratorAggregate
{
    /** @var Pop[] */
    private array $pops = [];

    public function reset(): void
    {
        unset($this->pops);
        $this->pops = [];
    }

    public function add(Pop $pop): void
    {
        $this->pops[] = $pop;
    }

    public function removeNotActive(): void
    {
        $this->pops = array_filter($this->pops, fn($pop) => $pop->isActive());
    }

    public function getIterator()
    {
        return new ArrayIterator($this->pops);
    }

    public function isEmpty(): bool
    {
        return empty($this->pops);
    }
}
