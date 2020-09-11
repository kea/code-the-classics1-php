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
    private Animator\Pop $animator;

    public function __construct(Animator\Pop $animator, int $type)
    {
        $this->type = $type;
        $this->animator = $animator;
    }

    public function update(float $deltaTime): void
    {
        if (!$this->isActive || $this->timer * 30 > 5) {
            $this->isActive = false;
            return;
        }
        $this->timer += $deltaTime;

        $this->animator->setFloat('timer', $this->timer);
        $this->animator->setFloat('type', (float)$this->type);
        $this->animator->update($deltaTime);
    }

    public function draw(Renderer $renderer): void
    {
        if (!$this->isActive) {
            return;
        }
        $this->animator->getSprite()->render($renderer);
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}