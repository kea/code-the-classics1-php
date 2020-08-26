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
    private \Cavern\Sprite\Pop $sprite;

    public function __construct(\Cavern\Sprite\Pop $sprite, int $type)
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
        $this->sprite->updateImage($this->timer, $this->type);
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