<?php

namespace PhpGame;

class Vector2Float
{
    public float $x;
    public float $y;

    public function __construct(float $x, float $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public static function zero(): self
    {
        return new self(.0, .0);
    }

    public function add(Vector2Float $addendum): self
    {
        $this->x += $addendum->x;
        $this->y += $addendum->y;

        return $this;
    }

    public function sub(Vector2Float $minuendum): self
    {
        $this->x -= $minuendum->x;
        $this->y -= $minuendum->y;

        return $this;
    }

    public function multiply(Vector2Float $multiplier): self
    {
        $this->x *= $multiplier->x;
        $this->y *= $multiplier->y;

        return $this;
    }

    public function multiplyFloat(float $multiplier): Vector2Float
    {
        $this->x *= $multiplier;
        $this->y *= $multiplier;

        return $this;
    }

    public function isZero(): bool
    {
        return $this->x === .0 && $this->y === .0;
    }

    public function isEqual(Vector2Float $vector2Float): bool
    {
        return $this->x === $vector2Float->x && $this->y === $vector2Float->y;
    }
}
