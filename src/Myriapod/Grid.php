<?php

declare(strict_types=1);

namespace Myriapod;

use Myriapod\Enemy\Rock;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

class Grid implements \IteratorAggregate
{
    /** @var array<[0-13], array<[0-24], Rock|null> */
    private array $cells;
    /** @var array<int, Rock> */
    private array $objects = [];

    public function __construct(private TextureRepository $textureRepository, private int $wave)
    {
        $this->cells = array_fill(0, 13, array_fill(0, 14, null));
    }

    public function addRock(int $x, int $y): void
    {
        $this->add(new Rock($this->textureRepository, new Vector2Float($x * 32, $y * 32), $this->wave, false), $x, $y);
    }

    private function add(mixed $object, int $x, int $y): void
    {
        if ($x < 0 || $x > 13 || $y < 0 || $y > 24) {
            throw new \OutOfBoundsException("Should be x < 14, y < 25");
        }
        $this->cells[$x][$y] = $object;
        $this->objects[] = $object;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->objects);
    }
}