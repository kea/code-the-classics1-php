<?php

declare(strict_types=1);

namespace PhpGame;

class Camera
{
    private \SDL_Rect $vieport;

    public function __construct(\SDL_Rect $vieport)
    {
        $this->vieport = $vieport;
    }

    public function follow(Vector2Int $center)
    {
        $this->vieport->x = $center->x() - $this->vieport->w / 2;
        $this->vieport->y = $center->y() - $this->vieport->h / 2;
    }

    public function toViewport(\SDL_Rect $rect): \SDL_Rect
    {
        $x = $rect->x - $this->vieport->x;
        $y = $rect->y - $this->vieport->y;

        return new \SDL_Rect($x, $y, $rect->w, $rect->h);
    }
}
