<?php

declare(strict_types=1);

namespace Myriapod\Bullet;

class Bullets implements \IteratorAggregate
{
    /** @var array<int, Bullet> */
    private array $bullets = [];

    public function append(Bullet $bullet): void
    {
        $this->bullets[] = $bullet;
        echo "B :".count($this->bullets)."\n";
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
}
