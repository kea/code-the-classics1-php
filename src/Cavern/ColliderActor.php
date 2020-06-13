<?php

namespace Cavern;

class ColliderActor
{
    protected \SDL_Point $position;
    protected int $width;
    protected int $height;

    public function __construct(\SDL_Point $position, int $width, int $height)
    {
        $this->position = $position;
        $this->width = $width;
        $this->height = $height;
    }

    public function move(float $dx, float $dy, float $speed, float $deltaTime)
    {
        $frameSpeed = $speed * $deltaTime;

        $this->position->x = $this->position->x + $dx * $frameSpeed;
        $this->position->y = $this->position->y + $dy * $frameSpeed;
    }

    public function getCollider(): \SDL_Rect
    {
        return new \SDL_Rect($this->position->x, $this->position->y, $this->width, $this->height);
    }
}
