<?php

namespace Cavern;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\Vector2Float;
use PhpGame\Vector2Int;

class Pop implements DrawableInterface
{
    public const TYPE_FRUIT = 0;
    public const TYPE_ORB = 1;
    private Vector2Float $position;
    private Vector2Int $dimension;
    private string $image = 'blank';
    private int $type;
    private float $timer = .0;
    private bool $isActive = true;

    /**
     * Pop constructor.
     * @param Vector2Float $position
     * @param Vector2Int   $dimension
     * @param int          $type
     */
    public function __construct(Vector2Float $position, Vector2Int $dimension, int $type)
    {
        $this->position = $position;
        $this->dimension = $dimension;
        $this->type = $type;
    }

    public function update(float $deltaTime): void
    {
        if (!$this->isActive || floor($this->timer * 30) > 5) {
            $this->isActive = false;
            return;
        }
        $this->timer += $deltaTime;
        $this->image = "pop".$this->type.floor($this->timer * 30);
    }

    public function draw(Renderer $renderer): void
    {
        if (!$this->isActive) {
            return;
        }

        $name = __DIR__.'/images/'.$this->image.'.png';

        $renderer->drawImage(
            $name,
            (int)($this->position->x - $this->dimension->x() / 2),
            (int)($this->position->y - $this->dimension->y()),
            $this->dimension->x(),
            $this->dimension->y()
        );
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}