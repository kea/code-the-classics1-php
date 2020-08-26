<?php

declare(strict_types=1);

namespace Cavern;

use PhpGame\SDL\Renderer;

class Bolt extends GravityActor
{
    private const SPEED = 7 * 60;
    private bool $isActive = true;
    private float $timer = .0;

    public function __construct(Sprite\Bolt $sprite, int $directionX)
    {
        parent::__construct($sprite);
        $this->directionX = $directionX;
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

        $this->sprite->updateImage($this->timer, $this->directionX);
    }

    public function draw(Renderer $renderer): void
    {
        if (!$this->isActive) {
            return;
        }

        $this->sprite->render($renderer);
        $renderer->drawRectangle($this->getCollider());
    }

    public function onCollision(ColliderActor $collider): void
    {
        $this->isActive = false;
    }
}
