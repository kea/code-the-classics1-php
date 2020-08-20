<?php

namespace Cavern;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\Vector2Float;
use PhpGame\Vector2Int;

class Orb extends ColliderActor implements DrawableInterface
{
    private const MAX_TIMER = 250 / 60;
    public float $blownTime = 0.1;
    private bool $floating = false;
    private float $timer = .0;
    private int $trappedEnemyType = Robot::TYPE_NONE;
    private string $image = 'orb0';
    private bool $isActive = true;
    private PopCollection $pops;
    private FruitCollection $fruits;

    public function __construct(
        Vector2Float $position,
        int $width,
        int $height,
        float $directionX,
        PopCollection $pops,
        FruitCollection $fruits
    ) {
        parent::__construct($position, $width, $height);
        $this->directionX = $directionX;
        $this->pops = $pops;
        $this->fruits = $fruits;
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
        if ($this->timer >= self::MAX_TIMER || $this->position->y <= -40) {
            $this->pop();
        }
        if ($this->timer < 0.15) {
            $this->image = "orb".(floor($this->timer * 20) % 3);
        } elseif ($this->hasTrappedEnemy()) {
            $this->image = "trap".$this->trappedEnemyType.(floor($this->timer * 15) % 8);
        } else {
            $this->image = "orb".round(3 + (floor(($this->timer - 0.15) * 7.5) % 4));
        }
    }

    private function pop(): void
    {
        $this->pops->add(new Pop($this->position, new Vector2Int(70, 70), Pop::TYPE_ORB));
        if ($this->hasTrappedEnemy()) {
            $fruit = $this->fruits->createFruit($this->position, $this->pops, $this->trappedEnemyType);
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
        $name = __DIR__.'/images/'.$this->image.'.png';

        $renderer->drawImage(
            $name,
            (int)($this->position->x - $this->width / 2),
            (int)($this->position->y - $this->height),
            $this->width,
            $this->height
        );
//        $renderer->drawRectangle($this->getCollider());
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
