<?php

declare(strict_types=1);

namespace Cavern;

use PhpGame\Animator;
use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\Sprite;

class Orb extends ColliderActor implements DrawableInterface
{
    private const MAX_TIMER = 250 / 60;
    public float $blownTime = 0.1;
    private bool $floating = false;
    private float $timer = .0;
    private int $trappedEnemyType = Robot::TYPE_NONE;
    private bool $isActive = true;
    private PopCollection $pops;
    private FruitCollection $fruits;
    private Animator $animator;

    public function __construct(
        Sprite $sprite,
        float $directionX,
        PopCollection $pops,
        FruitCollection $fruits
    ) {
        parent::__construct($sprite);
        $this->directionX = $directionX;
        $this->pops = $pops;
        $this->fruits = $fruits;
    }

    public function setAnimator(Animator $animator): void
    {
        $this->animator = $animator;
    }

    public function update(float $deltaTime): void
    {
        $this->timer += $deltaTime;

        if ($this->floating) {
            $this->move(0, -1, random_int(1, 2) * 60, $deltaTime);
        } elseif ($this->move($this->directionX, 0, 4 * 60, $deltaTime)) {
            $this->floating = true;
        }

        if ($this->timer >= $this->blownTime) {
            $this->floating = true;
        }
        if ($this->timer >= self::MAX_TIMER || $this->getPosition()->y <= -40) {
            $this->pop();
        }
        $this->animator->setFloat('timer', $this->timer);
        $this->animator->setInt('trappedEnemyType', $this->trappedEnemyType);
        $this->animator->update($deltaTime);
    }

    private function pop(): void
    {
        $this->pops->add($this->pops->createPop($this->getPosition(), Pop::TYPE_ORB));
        if ($this->hasTrappedEnemy()) {
            $fruit = $this->fruits->createFruit($this->getPosition(), $this->pops, $this->trappedEnemyType);
            $this->fruits->add($fruit);
        }
        //game.play_sound("pop", 4);
        $this->isActive = false;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->render($renderer);
    }

    public function hasTrappedEnemy(): bool
    {
        return $this->trappedEnemyType !== Robot::TYPE_NONE;
    }

    public function onCollision(ColliderActor $other): void
    {
        if (!$this->isActive) {
            return;
        }
        if ($other instanceof Robot && !$this->hasTrappedEnemy()) {
            $this->floating = true;
            $this->trappedEnemyType = $other->getType();
            //$this->playSound("trap", 4);
        }
        if ($other instanceof Bolt) {
            $this->pop();
        }
    }
}
