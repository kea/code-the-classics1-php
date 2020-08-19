<?php

namespace Cavern;

use PhpGame\Vector2Float;

class ColliderActor
{
    protected Vector2Float $position;
    protected int $width;
    protected int $height;

    protected array $anchor = ["center", "center"];
    protected ?Level $level = null;

    public function __construct(Vector2Float $position, int $width, int $height)
    {
        $this->position = $position;
        $this->width = $width;
        $this->height = $height;
    }

    public function setLevel(Level $level): void
    {
        $this->level = $level;
    }

    public function top(): float
    {
        $dy = $this->dxFromTop();

        return $this->position->y + $dy;
    }

    public function bottom(): float
    {
        $dy = $this->height - $this->dxFromTop();

        return $this->position->y + $dy;
    }

    public function move(float $dx, float $dy, float $speed, float $deltaTime): bool
    {
        $frameSpeed = (int)$speed * $deltaTime;
        $newPosition = clone $this->position;

        for ($i = 0; $i < $frameSpeed; ++$i) {
            $newPosition->add(new Vector2Float($dx, $dy));

            if ($newPosition->x < 70 || $newPosition->x > 730) {
                return true;
            }

            if ($this->level === null) {
                continue;
            }

            if ((($dy > 0 && Level::blockStartAt($newPosition->y)) ||
                    ($dx > 0 && Level::blockStartAt($newPosition->x)) ||
                    ($dx < 0 && Level::blockEndAt($newPosition->x)))
                && $this->level->blockAt($newPosition->x, $newPosition->y)) {
                return true;
            }
        }

        $this->position = $newPosition;

        return false;
    }

    public function getCollider(): \SDL_Rect
    {
        return new \SDL_Rect(
            $this->position->x - $this->width / 2,
            $this->position->y - $this->height,
            $this->width,
            $this->height
        );
    }

    /**
     * @return float|int
     */
    public function dxFromTop()
    {
        switch ($this->anchor[1]) {
            case 'center':
                $dy = $this->height / 2;
                break;
            case 'bottom':
                $dy = $this->height;
                break;
            default:
                $dy = 0;
        }

        return $dy;
    }
}
