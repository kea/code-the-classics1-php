<?php

namespace PhpGame;

class Transform
{
    private Vector2Float $position;
    private \SDL_Point $scale;

    public function __construct(Vector2Float $position)
    {
        $this->position = $position;
        $this->scale = new \SDL_Point(1, 1);
    }

    public function normalize(): array
    {
        $length = hypot($this->position->x, $this->position->y);

        return [$this->position->x / $length, $this->position->y / $length];
    }

    public function getScale(): \SDL_Point
    {
        return $this->scale;
    }

    public function getPosition(): Vector2Float
    {
        return $this->position;
    }
}
