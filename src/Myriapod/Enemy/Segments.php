<?php

declare(strict_types=1);

namespace Myriapod\Enemy;

class Segments implements \IteratorAggregate
{
    private const HEALT_ALTERNANCE_PER_WAVE = [[1, 1], [1, 2], [2, 2], [1, 1]];
    private array $segments = [];

    public function create(int $wave): void
    {
        $healthPattern = self::HEALT_ALTERNANCE_PER_WAVE[$wave % 4];
        $fast = $wave % 4 === 3;
        $this->segments = [];
        $num_segments = 8 + intdiv($wave, 4) * 2;
        for ($i = 0; $i < $num_segments; $i++) {
            $cellX = -1 - $i;
            $cellY = 0;

            $health = $healthPattern[$i % 2];
            $head = $i === 0;
            $this->segments[] = new Segment($cellX, $cellY, $health, $fast, $head);
        }
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->segments);
    }

    public function isEmpty(): bool
    {
        return count($this->segments) === 0;
    }
}