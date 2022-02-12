<?php

declare(strict_types=1);

namespace Myriapod\Enemy;

use PhpGame\TextureRepository;

class Segments implements \IteratorAggregate
{
    private const HEALTH_ALTERNATION_PER_WAVE = [[1, 1], [1, 2], [2, 2], [1, 1]];
    private array $segments = [];
    private SegmentPositions $segmentPositions;

    public function __construct(private TextureRepository $textureRepository, private Rocks $rocks)
    {
        $this->segmentPositions = new SegmentPositions();
    }

    public function create(int $wave): void
    {
        $healthPattern = self::HEALTH_ALTERNATION_PER_WAVE[$wave % 4];
        $fast = $wave % 4 === 3;
        $this->segments = [];
        $numSegments = 8 + intdiv($wave, 4) * 2;
        for ($i = 0; $i < $numSegments; $i++) {
            $cellX = -1 - $i + 10;
            $cellY = 10;

            $health = $healthPattern[$i % 2];
            $head = $i === 0;
            $this->segments[] = new Segment($this->textureRepository, $cellX, $cellY, $health, $fast, $head, $this->segmentPositions, $this->rocks);
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

    public function cleanUp(): void
    {
        $this->segmentPositions->reset();
        $this->segments = array_filter($this->segments, static fn(Segment $b) => $b->isAlive());
    }
}