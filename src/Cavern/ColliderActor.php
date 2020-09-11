<?php

namespace Cavern;

use PhpGame\Sprite;
use PhpGame\Vector2Float;
use SDL_Rect;

class ColliderActor
{
    protected float $directionX = 1.0;
    protected bool $collisionDetection = true;
    protected Sprite $sprite;

    protected ?Level $level = null;

    public function __construct(Sprite $sprite)
    {
        $this->sprite = $sprite;
    }

    public function setLevel(Level $level): void
    {
        $this->level = $level;
    }

    public function move(float $directionX, float $directionY, float $speed, float $deltaTime): bool
    {
        $frameSpeed = (int)$speed * $deltaTime;
        $newPosition = clone $this->getPosition();

        for ($i = 0; $i < $frameSpeed; ++$i) {
            $newPosition->add(new Vector2Float($directionX, $directionY));

            if ($newPosition->x < 70 || $newPosition->x > 730) {
                return true;
            }

            if ($this->level === null) {
                continue;
            }

            if ($this->collide($directionX, $directionY, $newPosition)) {
                return true;
            }
        }

        $this->sprite->setPosition($newPosition);

        return false;
    }

    public function getCollider(): SDL_Rect
    {
        return $this->sprite->getBoundedRect();
    }

    public function getPosition(): Vector2Float
    {
        return $this->sprite->getPosition();
    }

    public function setPosition(Vector2Float $position): void
    {
        $this->sprite->setPosition($position);
    }

    private function collide(float $dx, float $dy, Vector2Float $newPosition): bool
    {
        return (($dy > 0 && $this->level->blockStartAt($newPosition->y)) ||
                ($dx > 0 && $this->level->blockStartAt($newPosition->x)) ||
                ($dx < 0 && $this->level->blockEndAt($newPosition->x)))
            && $this->level->blockAt($newPosition->x, $newPosition->y);
    }
}
