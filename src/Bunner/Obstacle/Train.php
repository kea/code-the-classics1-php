<?php

declare(strict_types=1);

namespace Bunner\Obstacle;

class Train extends Mover
{
    public function getRandomSpriteName(): string
    {
        return "train".random_int(0, 2).($this->speed->x < 0 ? "0" : "1").'.png';
    }
}
