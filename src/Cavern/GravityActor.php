<?php

namespace Cavern;

class GravityActor extends ColliderActor
{
    private const MAX_FALL_SPEED = 10 * 60;
    protected const SCREEN_HEIGHT = 480;
    protected float $velocityY  = .0;
    protected bool $isLanded = false;

    public function sign(float $number)
    {
        return $number < 0 ? -1 : 1;
    }

    public function update(float $deltaTime): void
    {
        $this->velocityY = min($this->velocityY + 60, self::MAX_FALL_SPEED);

        if (!$this->collisionDetection) {
            $this->position->y += $this->velocityY * $deltaTime;

            return;
        }

        if ($this->move(0, $this->sign($this->velocityY), abs($this->velocityY), $deltaTime)) {
            $this->velocityY = 0;
            $this->isLanded = true;

            return;
        }

        if ($this->position->y >= self::SCREEN_HEIGHT) {
            $this->position->y = 1;
        }
    }
}