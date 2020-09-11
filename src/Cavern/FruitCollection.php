<?php

namespace Cavern;

use ArrayIterator;
use IteratorAggregate;
use PhpGame\Animator;
use PhpGame\Vector2Float;

class FruitCollection implements IteratorAggregate
{
    /** @var Fruit[] */
    private array $fruits = [];
    private Level $level;
    private Animator $animator;

    public function __construct(\Cavern\Animator\Fruit $animator)
    {
        $this->animator = $animator;
    }

    public function newLevel(Level $level): void
    {
        unset($this->fruits);
        $this->fruits = [];
        $this->level = $level;
    }

    public function createFruit(Vector2Float $position, PopCollection $pops, ?int $trappedEnemyType = null): Fruit
    {
        $animator = clone $this->animator;
        $animator->getSprite()->setPosition($position);
        $fruit = new Fruit($animator, $pops, $trappedEnemyType ?? Robot::TYPE_NORMAL);
        $fruit->setLevel($this->level);

        return $fruit;
    }

    public function add(Fruit $fruit): void
    {
        $this->fruits[] = $fruit;
    }

    public function removeNotActive(): void
    {
        $this->fruits = array_filter($this->fruits, fn($fruit) => $fruit->isActive());
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->fruits);
    }

    public function isEmpty(): bool
    {
        return empty($this->fruits);
    }
}
