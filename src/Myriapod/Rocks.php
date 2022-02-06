<?php

declare(strict_types=1);

namespace Myriapod;

use Myriapod\Enemy\Rock;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

class Rocks implements \IteratorAggregate
{
    private const WIDTH = 14;
    private const HEIGHT = 25;
    /** @var array<[0-24], array<[0-13], Rock|null> */
    private array $cells;
    /** @var array<int, Rock> */
    private array $objects = [];

    public function __construct(private TextureRepository $textureRepository, private int $wave)
    {
        $this->cells = array_fill(0, 25, array_fill(0, 14, null));
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
        $this->cells[$y][$x] = $object;
        $this->objects[] = $object;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->objects);
    }

    public function addRockRandom(): void
    {
        $created = false;
        do {
            $x = random_int(0, self::WIDTH - 1);
            $y = random_int(1, self::HEIGHT - 3);
            if ($this->cells[$y][$x] === null) {
                $this->addRock($x, $y);
                $created = true;
            }
        } while (!$created);
    }

    public function count(): int
    {
        return count($this->objects);
    }
}
