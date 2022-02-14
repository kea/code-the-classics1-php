<?php

declare(strict_types=1);

namespace Myriapod\Bullet;

use Myriapod\Enemy\FlyingEnemy;
use Myriapod\Enemy\Rocks;
use Myriapod\Enemy\Segments;

class Bullets implements \IteratorAggregate
{
    /** @var array<int, Bullet> */
    private array $bullets = [];

    public function append(Bullet $bullet): void
    {
        $this->bullets[] = $bullet;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->bullets);
    }

    public function reset(): void
    {
        $this->bullets = [];
    }

    public function remove(Bullet $object): void
    {
        $this->bullets = array_filter($this->bullets, static fn($b) => $b !== $object);
    }

    public function cleanUp(): void
    {
        $this->bullets = array_filter($this->bullets, static fn($b) => $b->getPosition()->y > 0);
    }

    public function checkCollision(Rocks $rocks, Segments $segments, ?FlyingEnemy $flyingEnemy): void
    {
        foreach ($this->bullets as $bullet) {
            $gridCell = $this->pos2cell($bullet->getPosition()->x, $bullet->getPosition()->y);
            if ($rocks->damage($gridCell[0], $gridCell[1], 1, true)) {
                $this->remove($bullet);

                return;
            }

            if ($segments->damage($bullet->getCollider())) {
                $this->remove($bullet);

                return;
            }

            if ($flyingEnemy && $flyingEnemy->collideWith($bullet->getCollider())) {
                $flyingEnemy->damage(1);
                $this->remove($bullet);

                return;
            }

        }
    }

    private function pos2cell(int|float $x, int|float $y): array
    {
        return [intdiv((int)$x - 16, 32), intdiv((int)$y, 32)];
    }
}
