<?php

namespace Cavern;

use PhpGame\SDL\Renderer;
use PhpGame\Vector2Float;

class Bolt extends GravityActor
{
    private const SPEED = 7 * 60;
    private int $directionX;
    private bool $isActive = true;
    private float $timer = .0;

    public function __construct(Vector2Float $position, int $width, int $height, int $directionX)
    {
        parent::__construct($position, $width, $height);
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
    }

    public function draw(Renderer $renderer): void
    {
        if (!$this->isActive) {
            return;
        }

        $directionIdx = $this->directionX > 0 ? "1" : "0";
        $animFrame = floor($this->timer / 4) % 2;
        $name = __DIR__.'/images/bolt'.$directionIdx.$animFrame.'.png';

        $renderer->drawImage(
            $name,
            (int)($this->position->x - $this->width / 2),
            (int)($this->position->y - $this->height),
            $this->width,
            $this->height
        );
        $renderer->drawRectangle($this->getCollider());
    }

    public function onCollision(ColliderActor $collider): void
    {
        $this->isActive = false;
    }
}
