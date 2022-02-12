<?php

declare(strict_types=1);

namespace Myriapod\Explosion;

use PhpGame\TextureRepository;

class Explosions implements \IteratorAggregate
{
    /** @var array<int, Explosion> */
    private array $explosions = [];

    public function __construct(private TextureRepository $textureRepository)
    {
    }

    public function append(Explosion $explosion): void
    {
        $this->explosions[] = $explosion;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->explosions);
    }

    public function cleanUp(): void
    {
        $this->explosions = array_filter($this->explosions, static fn($b) => $b->isRunning());
    }

    public function addExplosion(\PhpGame\Vector2Float $position, int $type): void
    {
        $this->append(new Explosion($this->textureRepository, $position, $type));
    }
}
