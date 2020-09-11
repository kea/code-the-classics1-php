<?php

namespace Cavern;

use ArrayIterator;
use IteratorAggregate;
use PhpGame\Vector2Float;

class BoltCollection implements IteratorAggregate
{
    /** @var Bolt[] */
    private array $bolts = [];
    private \Cavern\Animator\Bolt $animation;

    public function __construct(\Cavern\Animator\Bolt $animation)
    {
        $this->animation = $animation;
    }

    public function reset(): void
    {
        unset($this->bolts);
        $this->bolts = [];
    }

    public function add(Bolt $bolt): void
    {
        $this->bolts[] = $bolt;
    }

    public function create(Vector2Float $position, int $directionX): Bolt
    {
        $animation = clone $this->animation;
        $animation->getSprite()->setPosition($position);

        return new Bolt($animation, $directionX);
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
