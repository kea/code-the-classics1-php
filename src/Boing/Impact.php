<?php

namespace Boing;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Screen;

class Impact implements DrawableInterface
{
    private float $x;
    private float $y;
    private Animation $animation;

    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
        $this->animation = new Animation(
            [
                __DIR__.'/images/impact0.png',
                __DIR__.'/images/impact1.png',
                __DIR__.'/images/impact2.png',
                __DIR__.'/images/impact3.png',
                __DIR__.'/images/impact4.png',
            ]
        );
        $this->animation->startAnimation();
    }

    public function getAnimation(): Animation
    {
        return $this->animation;
    }

    public function update(float $deltaTime): void
    {
    }

    public function draw(Screen $screen): void
    {
        if (!$this->animation->isRunning()) {
            return;
        }

        $screen->drawImage(
            $this->animation->getCurrentFrame(),
            (int)($this->x - 75 / 2),
            (int)($this->y - 75 / 2),
            75,
            75
        );
    }
}

