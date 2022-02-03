<?php

declare(strict_types=1);

namespace Cavern;

use PhpGame\SDL\Renderer;

class Bolt extends GravityActor
{
    private const SPEED = 7 * 60;
    private bool $isActive = true;
    private float $timer = .0;
    private Animator\Bolt $animator;

    public function __construct(Animator\Bolt $animator, int $directionX)
    {
        parent::__construct($animator->getSprite());
        $this->directionX = $directionX;
        $this->animator = $animator;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function update(float $deltaTime): void
    {
        $this->timer += $deltaTime;
        if ($this->move($this->directionX, .0, self::SPEED, $deltaTime)) {
            $this->isActive = false;
        }

        $this->animator->setFloat('timer', $this->timer);
        $this->animator->setFloat('directionX', $this->directionX);
        $this->animator->update($deltaTime);
    }

    public function draw(Renderer $renderer): void
    {
        if (!$this->isActive) {
            return;
        }

        $this->animator->getSprite()->draw($renderer);
    }

    public function onCollision(ColliderActor $collider): void
    {
        $this->isActive = false;
    }
}
