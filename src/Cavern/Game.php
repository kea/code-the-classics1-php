<?php

declare(strict_types=1);

namespace Cavern;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\SDL\Screen;
use PhpGame\SoundManager;
use PhpGame\Vector2Float;

class Game implements DrawableInterface
{
    private ?SoundManager $soundManager;
    /** @var array|DrawableInterface[] */
    private array $bolts = [];
    /** @var array|DrawableInterface[] */
    private array $enemies = [];

    private ?Player $player = null;
    private Level $level;
    private array $pendingEnemies;
    private float $timer = 0;
    private float $nextFruit = 0;
    private OrbCollection $orbs;
    private PopCollection $pops;
    private FruitCollection $fruits;

    public function __construct(
        Level $level,
        OrbCollection $orbs,
        FruitCollection $fruits,
        PopCollection $pops,
        ?Player $player = null
    ) {
        $this->player = $player;
        $this->level = $level;
        $this->orbs = $orbs;
        $this->fruits = $fruits;
        $this->pops = $pops;
    }

    public function start()
    {
        $this->nextLevel();
    }

    public function fireProbability(): float
    {
        return 0.001 + (0.0001 * min(100, $this->level->level));
    }

    public function maxEnemies(): int
    {
        return (int)min(($this->level->level + 6) / 2, 8);
    }

    public function update(float $deltaTime): void
    {
        $this->timer += $deltaTime;

        $updatablesContainer = $this->getUpdatableObjects();

        foreach ($updatablesContainer as $updatables) {
            foreach ($updatables as $updatable) {
                $updatable->update($deltaTime);
            }
        }

        if ($this->player) {
            $this->player->update($deltaTime);

            foreach ($this->fruits as $fruit) {
                if (SDL_HasIntersection($this->player->getCollider(), $fruit->getCollider())) {
                    $fruit->onCollision($this->player);
                }
            }
        }

        $this->fruits->removeNotActive();
        $this->bolts = array_filter($this->bolts, fn($bolt) => $bolt->isActive());
        $this->pops->removeNotActive();
        $this->orbs->removeNotActive();
        $this->enemies = array_filter($this->enemies, fn($enemy) => $enemy->isActive());

        $this->nextFruit += $deltaTime;
        if (($this->nextFruit > 1.7) && (count($this->pendingEnemies) + count($this->enemies) > 0)) {
            $this->nextFruit -= 1.7;
            $fruit = new Fruit(new Vector2Float(random_int(70, 730), random_int(75, 400)), 54, 54, $this->pops);
            $fruit->setLevel($this->level);
            $this->fruits->add($fruit);
        }
    }

    public function draw(Renderer $renderer): void
    {
        $this->level->draw($renderer);

        $updatablesContainer = $this->getUpdatableObjects();
        if ($this->player) {
            $updatablesContainer[] = [$this->player];
        }

        foreach ($updatablesContainer as $updatables) {
            foreach ($updatables as $updatable) {
                $updatable->draw($renderer);
            }
        }
    }

    private function drawScores(Screen $screen): void
    {
    }

    public function playSound(string $name, int $count = 1): void
    {
        if ($this->soundManager === null) {
            return;
        }

        if ($this->soundManager->getMusicVolume() === 0) {
            return;
        }

        $name .= random_int(0, $count - 1);

        $this->soundManager->playSound($name.'.ogg');
    }

    /**
     * @param SoundManager $soundManager
     */
    public function setSoundManager(SoundManager $soundManager): void
    {
        $this->soundManager = $soundManager;
    }

    private function nextLevel(): void
    {
        $this->timer = -1;

        if ($this->player) {
            $this->player->reset();
        }

        $this->fruits->reset();
        $this->createEnemies();
        $this->level->buildNextLevel();
        $this->soundManager->playSound("level");
    }

    private function getUpdatableObjects(): array
    {
        return [$this->fruits, $this->bolts, $this->pops, $this->orbs];
    }

    private function createEnemies(): void
    {
        $this->bolts = [];
        $this->enemies = [];
        $this->pops->reset();
        $this->orbs->reset();

        $enemiesCount = 10 + $this->level->level;
        $strongEnemiesCount = 1 + (int)($this->level->level / 1.5);
        $weakEnemiesCount = $enemiesCount - $strongEnemiesCount;
        $this->pendingEnemies = array_merge(
            array_fill(0, $strongEnemiesCount, Robot::TYPE_AGGRESSIVE),
            array_fill($strongEnemiesCount, $weakEnemiesCount, Robot::TYPE_NORMAL)
        );
        shuffle($this->pendingEnemies);
        // playSound('level', 1);
    }
}
