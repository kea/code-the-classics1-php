<?php

declare(strict_types=1);

namespace Bunner;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;

class Game implements DrawableInterface, SoundEmitterInterface
{
    use SoundEmitterTrait;

    public function update(float $deltaTime): void
    {
    }

    public function draw(Renderer $renderer): void
    {
    }

    public function start(): void
    {
    }

    public function isGameOver(): bool
    {
        return false;
    }
}
