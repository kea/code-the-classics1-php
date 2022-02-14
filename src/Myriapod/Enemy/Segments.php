<?php

declare(strict_types=1);

namespace Myriapod\Enemy;

use Myriapod\Explosion\Explosion;
use Myriapod\Explosion\Explosions;
use Myriapod\Score;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

class Segments implements \IteratorAggregate
{
    private const HEALTH_ALTERNATION_PER_WAVE = [[1, 1], [1, 2], [2, 2], [1, 1]];
    private array $segments = [];
    private SegmentPositions $segmentPositions;

    public function __construct(
        private TextureRepository $textureRepository,
        private Rocks $rocks,
        private Explosions $explosions
    ) {
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
            $this->segments[] = new Segment(
                $this->textureRepository,
                $cellX,
                $cellY,
                $health,
                $fast,
                $head,
                $this->segmentPositions,
                $this->rocks
            );
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

    public function damage(\SDL_Rect $bulletCollider, Score $score): bool
    {
        /** @var Segment $segment */
        foreach ($this->segments as $segment) {
            if ($segment->collideWith($bulletCollider)) {
                $this->explosions->addExplosion($segment->getPosition()->sub(new Vector2Float(16, 16)), Explosion::ENEMY);
                $segment->damage(1);

                // @todo check
                // if obj.health == 0 and not game.grid[obj.cell_y][obj.cell_x] and game.allow_movement(game.player.x, game.player.y, obj.cell_x, obj.cell_y):
                $position = $segment->pos2cell();
                $this->rocks->addRock($position[0], $position[1], random_int(0, 100) < 20);

                // @todo $this->playSound("segment_explode0.ogg");
                $score->add(10);

                return true;
            }
        }

        return false;
    }
}