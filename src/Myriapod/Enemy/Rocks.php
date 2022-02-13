<?php

declare(strict_types=1);

namespace Myriapod\Enemy;

use Myriapod\Explosion\Explosions;
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

    public function __construct(
        private TextureRepository $textureRepository,
        private int $wave,
        private SoundManager $soundManager,
        private Explosions $explosions
    ) {
        $this->cells = array_fill(0, self::HEIGHT, array_fill(0, self::WIDTH, null));
    }

    public function addRock(int $x, int $y, bool $totem = false): void
    {
        $rock = new Rock(
            $this->textureRepository,
            new Vector2Float($x * 32, $y * 32),
            $this->wave,
            $totem,
            $this->explosions
        );
        $rock->setSoundManager($this->soundManager);
        $this->add($rock, $x, $y);
        if ($totem) {
            $this->soundManager->playSound("totem_create0.ogg");
        }
    }

    private function add(mixed $object, int $x, int $y): void
    {
        if ($x < 0 || $x >= self::WIDTH || $y < 0 || $y >= self::HEIGHT) {
            throw new \OutOfBoundsException("Position should be x < ".self::WIDTH.", y < ".self::HEIGHT);
        }
        $this->cells[$y][$x] = $object;
        $this->rocks[] = $object;
    }

    /**
     * @return \ArrayIterator<int, Rock>
     */
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

    public function damage(int $x, int $y, int $damage, bool $isBullet): bool
    {
        if (!$this->isOccupied($x, $y)) {
            return false;
        }

        $this->cells[$y][$x]->damage($damage, $isBullet);
        if (!$this->cells[$y][$x]->isAlive()) {
            $rock = $this->cells[$y][$x];
            unset($this->rocks[array_search($rock, $this->rocks, true)], $this->cells[$y][$x], $rock);
            $this->cells[$y][$x] = null;
        }

        return true;
    }

    public function clearRocksForRespawn(\SDL_Rect $collider): void
    {
        foreach ($this->rocks as $key => $rock) {
            if (!$collider->HasIntersection($rock->getCollider())) {
                continue;
            }
            unset($this->rocks[$key]);
            foreach ($this->cells as $y => $row) {
                $x = array_search($rock, $row, true);
                if ($x !== false) {
                    unset($this->cells[$y][$x]);
                    $this->cells[$y][$x] = null;

                    return;
                }
            }
        }
    }
}
