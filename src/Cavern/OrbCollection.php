<?php

namespace Cavern;

use ArrayIterator;
use IteratorAggregate;
use PhpGame\Vector2Float;

class OrbCollection implements IteratorAggregate
{
    private const MAX_ORBS = 5;
    /** @var Orb[] */
    private array $orbs = [];
    private FruitCollection $fruits;
    private PopCollection $pops;

    /**
     * OrbCollection constructor.
     * @param FruitCollection $fruits
     * @param PopCollection   $pops
     */
    public function __construct(FruitCollection $fruits, PopCollection $pops)
    {
        $this->fruits = $fruits;
        $this->pops = $pops;
    }

    public function reset(): void
    {
        unset($this->orbs);
        $this->orbs = [];
    }

    public function createOrb(float $x, float $y, float $direction): ?Orb
    {
        if (count($this->orbs) >= self::MAX_ORBS) {
            return null;
        }

        return new Orb(new Vector2Float($x, $y), 70, 70, $direction, $this->pops, $this->fruits);
    }

    public function add(Orb $orb): void
    {
        $this->orbs[] = $orb;
    }

    public function removeNotActive(): void
    {
        $this->orbs = array_filter($this->orbs, fn($orb) => $orb->isActive());
    }

    public function getIterator()
    {
        return new ArrayIterator($this->orbs);
    }
}
