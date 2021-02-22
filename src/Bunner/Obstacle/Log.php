<?php

declare(strict_types=1);

namespace Bunner\Obstacle;

class Log extends Mover
{
    public function getRandomSpriteName(): string
    {
        return "log".random_int(0, 1).'.png';
    }
}