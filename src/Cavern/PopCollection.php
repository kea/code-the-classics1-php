<?php

namespace Cavern;

use ArrayIterator;
use IteratorAggregate;
use PhpGame\Vector2Float;

class PopCollection implements IteratorAggregate
{
    /** @var Pop[] */
    private array $pops = [];

    private \Cavern\Animator\Pop $animator;

    public function __construct(\Cavern\Animator\Pop $animator)
    {
        $this->animator = $animator;
    }

    public function createPop(Vector2Float $position, $type): Pop
    {
        $animator = clone $this->animator;
        $animator->getSprite()->setPosition($position);

        return new Pop($animator, $type);
    }

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
