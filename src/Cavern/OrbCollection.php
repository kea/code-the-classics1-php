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
    private Animator\Orb $animator;

    public function __construct(\Cavern\Animator\Orb $animator, FruitCollection $fruits, PopCollection $pops)
    {
        $this->fruits = $fruits;
        $this->pops = $pops;
        $this->animator = $animator;
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

        $animator = clone $this->animator;
        $animator->getSprite()->setPosition(new Vector2Float($x, $y));

        $orb = new Orb($animator->getSprite(), $direction, $this->pops, $this->fruits);
        $orb->setAnimator($animator);

        return $orb;
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
