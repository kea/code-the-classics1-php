<?php

namespace Cavern;

class GravityActor extends ColliderActor
{
    private const MAX_FALL_SPEED = 10 * 60;
    const HEIGHT = 400;
    protected float $velocityY  = .0;
    protected bool $isLanded = false;

    public function sign(float $number)
    {
        return $number < 0 ? -1 : 1;
    }

    public function update(float $deltaTime)
    {
        if ($this->isLanded) {
            return;
        }
        $this->velocityY = min($this->velocityY + 60, self::MAX_FALL_SPEED);
        $this->position->y += $this->velocityY * $deltaTime;
        if ($this->position->y >= self::HEIGHT) {
            $this->position->y = 1;
        }

        //$this->move();
    }
}