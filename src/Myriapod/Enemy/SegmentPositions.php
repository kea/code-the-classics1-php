<?php

declare(strict_types=1);

namespace Myriapod\Enemy;

class SegmentPositions
{
    private array $positions = [];

    public function occupy(int $x, int $y, ?int $edge = null): void
    {
        $this->positions[] = [$x, $y, $edge];
    }

    public function isOccupied(int $x, int $y, ?int $edge = null): bool
    {
        return in_array([$x, $y, $edge], $this->positions, true);
    }

    public function reset(): void
    {
        $this->positions = [];
    }
}