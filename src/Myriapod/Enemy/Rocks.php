<?php

declare(strict_types=1);

namespace Myriapod\Enemy;

use PhpGame\SoundManager;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

class Rocks implements \IteratorAggregate
{
    public const WIDTH = 14;
    public const HEIGHT = 25;
    /** @var array<[0-24], array<[0-13], Rock|null> */
    private array $cells;
    /** @var array<int, Rock> */
    private array $rocks = [];

    public function __construct(private TextureRepository $textureRepository, private int $wave, private SoundManager $soundManager)
    {
        $this->cells = array_fill(0, self::HEIGHT, array_fill(0, self::WIDTH, null));
    }

    public function addRock(int $x, int $y): void
    {
        $rock = new Rock($this->textureRepository, new Vector2Float($x * 32, $y * 32), $this->wave, false);
        $rock->setSoundManager($this->soundManager);
        $this->add($rock, $x, $y);
    }

    private function add(mixed $object, int $x, int $y): void
    {
        if ($x < 0 || $x >= self::WIDTH || $y < 0 || $y >= self::HEIGHT) {
            throw new \OutOfBoundsException("Position should be x < ".self::WIDTH.", y < ".self::HEIGHT);
        }
        $this->cells[$y][$x] = $object;
        $this->rocks[] = $object;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->rocks);
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
        return count($this->rocks);
    }

    public function isOccupied(int $x, int $y): bool
    {
        return !empty($this->cells[$y][$x]);
    }

    public function damage(int $x, int $y, int $damage): void
    {
        if (!$this->isOccupied($x, $y)) {
            return;
        }

        $this->cells[$y][$x]->damage($damage);
        if (!$this->cells[$y][$x]->isAlive()) {
            $rock = $this->cells[$y][$x];
            unset($this->rocks[array_search($rock, $this->rocks, true)], $this->cells[$y][$x], $rock);
        }
    }
}
