<?php

namespace Cavern;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;

class Pop implements DrawableInterface
{
    public const TYPE_FRUIT = 0;
    public const TYPE_ORB = 1;
    private int $type;
    private float $timer = .0;
    private bool $isActive = true;
    private Animator\Pop $sprite;

    public function __construct(Animator\Pop $sprite, int $type)
    {
        $this->type = $type;
        $this->sprite = $sprite;
    }

    public function update(float $deltaTime): void
    {
        if (!$this->isActive || $this->timer * 30 > 5) {
            $this->isActive = false;
            return;
        }
        $this->timer += $deltaTime;

        $this->sprite->setFloat('timer', $this->timer);
        $this->sprite->setFloat('type', (float)$this->type);
        $this->sprite->update($deltaTime);
    }

    public function draw(Renderer $renderer): void
    {
        if (!$this->isActive) {
            return;
        }
        $this->sprite->render($renderer);
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}