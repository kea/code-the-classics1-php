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
    private Animator\Orb $sprite;

    public function __construct(\Cavern\Animator\Orb $sprite, FruitCollection $fruits, PopCollection $pops)
    {
        $this->fruits = $fruits;
        $this->pops = $pops;
        $this->sprite = $sprite;
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

        $sprite = clone $this->sprite;
        $sprite->setPosition(new Vector2Float($x, $y));

        return new Orb($sprite, $direction, $this->pops, $this->fruits);
    }

    public function add(Orb $orb): void
    {
        $this->orbs[] = $orb;
    }

    public function removeNotActive(): void
    {
        $this->orbs = array_filter($this->orbs, fn($orb) => $orb->isActive());
    }

    /** @return ArrayIterator<Bolt> */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->orbs);
    }

    public function hasTrappedEnemies(): bool
    {
        return count(array_filter($this->orbs, fn($orb) => $orb->hasTrappedEnemy())) !== 0;
    }
}
