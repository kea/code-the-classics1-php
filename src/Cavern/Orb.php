<?php

namespace Cavern;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\Vector2Float;

class Orb extends ColliderActor implements DrawableInterface
{
    private const MAX_TIMER = 250 / 60;
    private int $direction;
    public float $blownTime = 0.1;
    private bool $floating = false;
    private float $timer = .0;
    private $trappedEnemyType;
    private string $image = 'orb0';
    private bool $isActive = true;

    public function __construct(Vector2Float $position, int $width, int $height, float $direction)
    {
        parent::__construct($position, $width, $height);
        $this->direction = $direction;
    }

    public function update(float $deltaTime): void
    {
        $this->timer += $deltaTime;

        if ($this->floating) {
            $this->move(0, -1, random_int(1, 2) * 60, $deltaTime);
        } elseif ($this->move($this->direction, 0, 4 * 60, $deltaTime)) {
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
        } elseif ($this->trappedEnemyType !== null) {
            $this->image = "trap".$this->trappedEnemyType.(floor($this->timer * 15) % 8);
        } else {
            $this->image = "orb".round(3 + (floor(($this->timer - 0.15) * 7.5) % 4));
        }
    }

    private function pop()
    {
/*        game.pops.append(Pop(self.pos, 1));
        if (self.trapped_enemy_type != None) {
            game.fruits.append(Fruit(self.pos, self.trapped_enemy_type))
        }
        game.play_sound("pop", 4);*/
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
}