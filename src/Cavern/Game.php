<?php

declare(strict_types=1);

namespace Cavern;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\SoundManager;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

class Game implements DrawableInterface
{
    private ?SoundManager $soundManager;
    private ?Player $player = null;
    private Level $level;
    private array $pendingEnemies;
    private float $timer = 0;
    private float $nextFruitTimer = 0;
    private float $nextEnemyTimer = 0;
    private OrbCollection $orbs;
    private PopCollection $pops;
    private FruitCollection $fruits;
    private BoltCollection $bolts;
    private RobotCollection $enemies;
    private StatusBar $statusBar;
    private TextureRepository $textureRepository;

    public function __construct(
        Level $level,
        OrbCollection $orbs,
        FruitCollection $fruits,
        PopCollection $pops,
        TextureRepository $textureRepository,
        ?Player $player = null
    ) {
        $this->player = $player;
        $this->level = $level;
        $this->orbs = $orbs;
        $this->fruits = $fruits;
        $this->pops = $pops;
        $this->bolts = new BoltCollection(new \Cavern\Animator\Bolt($textureRepository));
        $this->enemies = new RobotCollection();
        $this->statusBar = new StatusBar();
        $this->textureRepository = $textureRepository;
    }

    public function start(): void
    {
        $this->nextLevel();
    }

    public function maxEnemies(): int
    {
        return (int)min(($this->level->level + 6) / 2, 8);
    }

    public function update(float $deltaTime): void
    {
        $this->timer += $deltaTime;

        $updatablesCollections = $this->getUpdatableObjects();

        foreach ($updatablesCollections as $updatables) {
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

            foreach ($this->bolts as $bolt) {
                foreach ($this->orbs as $orb) {
                    if (SDL_HasIntersection($bolt->getCollider(), $orb->getCollider())) {
                        $bolt->onCollision($orb);
                        $orb->onCollision($bolt);
                        continue;
                    }
                }
                if (SDL_HasIntersection($bolt->getCollider(), $this->player->getCollider())) {
                    $bolt->onCollision($this->player);
                    $this->player->onCollision($bolt);
                }
            }
        }

        foreach ($this->orbs as $orb) {
            foreach ($this->enemies as $enemy) {
                if (SDL_HasIntersection($orb->getCollider(), $enemy->getCollider())) {
                    $enemy->onCollision($orb);
                    $orb->onCollision($enemy);
                    continue 2;
                }
            }
        }

        $this->fruits->removeNotActive();
        $this->bolts->removeNotActive();
        $this->pops->removeNotActive();
        $this->orbs->removeNotActive();
        $this->enemies->removeNotActive();

        $this->nextFruitTimer += $deltaTime;
        if (($this->nextFruitTimer > 1.7) && (count($this->pendingEnemies) > 0 || !$this->enemies->isEmpty())) {
            $this->nextFruitTimer -= 1.7;
            $fruit = $this->fruits->createFruit(new Vector2Float(random_int(70, 730), random_int(75, 400)), $this->pops);
            $fruit->setLevel($this->level);
            $this->fruits->add($fruit);
        }

        $this->nextEnemyTimer += $deltaTime;
        if ($this->nextEnemyTimer >= 1.35 && count($this->pendingEnemies) > 0 && count($this->enemies) < $this->maxEnemies()) {
            $this->nextEnemyTimer -= 1.35;
            $robotType = array_pop($this->pendingEnemies);
            $pos = new Vector2Float($this->level->getRobotSpawnX(), -30);
            $animator = new \Cavern\Animator\Robot($this->textureRepository);
            $animator->getSprite()->setPosition($pos);
            $robot = new Robot($animator, $robotType, $this->orbs, $this->bolts, $this->level, $this->player);
            $this->enemies->add($robot);
        }

        if ((count($this->pendingEnemies) === 0)
            && $this->fruits->isEmpty()
            && $this->enemies->isEmpty()
            && $this->pops->isEmpty()
            && !$this->orbs->hasTrappedEnemies())
        {
            $this->nextLevel();
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

        if (!$this->isGameOver()) {
            $this->statusBar->draw($renderer, $this->player, $this->level);
        }
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

        $this->fruits->newLevel($this->level);
        $this->createEnemies();
        $this->level->buildNextLevel();
        $this->soundManager->playSound("level");
    }

    private function getUpdatableObjects(): array
    {
        return [$this->fruits, $this->bolts, $this->pops, $this->orbs, $this->enemies];
    }

    private function createEnemies(): void
    {
        $this->bolts->reset();
        $this->enemies->reset();
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

    public function isGameOver(): bool
    {
        return $this->player === null || $this->player->getLives() < 0;
    }
}
