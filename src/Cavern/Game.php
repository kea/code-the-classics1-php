<?php

declare(strict_types=1);

namespace Cavern;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\SDL\Screen;
use PhpGame\SoundManager;

class Game implements DrawableInterface
{
    const LEVEL_X_OFFSET = 50;
    const GRID_BLOCK_SIZE = 25;
    private int $fieldWidth;
    private int $fieldHeight;
    private ?SoundManager $soundManager;
    /** @var array|DrawableInterface[] */
    private array $fruits = [];
    /** @var array|DrawableInterface[] */
    private array $bolts = [];
    /** @var array|DrawableInterface[] */
    private array $enemies = [];
    /** @var array|DrawableInterface[] */
    private array $pops = [];
    /** @var array|DrawableInterface[] */
    private array $orbs = [];
    /** @var array|Block[] */
    private array $backgroundBlocks;

    private ?Player $player = null;
    private int $level;
    private int $levelColor;
    private array $pendingEnemies;
    private $grid;
    private float $timer = 0;
    private array $levels;
    private float $nextFruit = 0;

    public function __construct(
        int $fieldWidth,
        int $fieldHeight,
        ?Player $player = null
    ) {
        $this->fieldWidth = $fieldWidth;
        $this->fieldHeight = $fieldHeight;
        $this->levelColor = -1;
        $this->level = -1;
        $this->player = $player;
        $this->loadLevels();
    }

    public function start()
    {
        $this->nextLevel();
    }

    public function update(float $deltaTime): void
    {
        $this->detectCollision();
        $this->timer += $deltaTime;

        $updatablesContainer = $this->getUpdatableObjects();

        foreach ($updatablesContainer as $updatables) {
            foreach ($updatables as $updatable) {
                $updatable->update($deltaTime);
            }
        }

        $this->fruits = array_filter($this->fruits, fn($fruit) => $fruit->isAlive());
//        $this->bolts = array_filter($this->bolts, fn($bolt) => $bolt->isActive());
//        $this->pops = array_filter($this->pops, fn($pop) => !$pop->animation->isStopped());
//        $this->orbs = array_filter($this->orbs, fn($orb) => $orb->isActive());

        $this->nextFruit += $deltaTime;
        if ($this->nextFruit > 1.7) {
            $this->nextFruit -= 1.7;
            // fruit come se piovesse
        }
    }

    private function detectCollision()
    {
        if (!$this->player) {
            return;
        }

        $player = $this->player;

        foreach ($this->backgroundBlocks as $bgBlock) {
            if (\SDL_HasIntersection($player->getCollider(), $bgBlock->getCollider())) {
                $player->onCollision($bgBlock);
            }
        }
        foreach ($this->fruits as $fruit) {
            if (\SDL_HasIntersection($player->getCollider(), $fruit->getCollider())) {
                $player->onCollision($fruit);
                $fruit->onCollision($player);
            }
        }
    }

    public function draw(Renderer $renderer): void
    {
        $this->drawBackground($renderer);

        $updatablesContainer = $this->getUpdatableObjects();

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
        $this->levelColor = ($this->levelColor + 1) % 4;
        ++$this->level;
        $this->timer = -1;

        if ($this->player) {
            $this->player->reset();
        }

        $this->fruits = [];
        $this->createEnemies();
        $this->createBackground();
        $this->soundManager->playSound("level");
    }

    private function loadLevels(): void
    {
        $this->levels = json_decode(file_get_contents(__DIR__.'/levels/levels.json'), true, 512, JSON_THROW_ON_ERROR);
    }

    private function getUpdatableObjects(): array
    {
        $updatablesContainer = [$this->fruits, $this->bolts, $this->pops, $this->orbs];
        if ($this->player) {
            $updatablesContainer[] = [$this->player];
        }

        return $updatablesContainer;
    }


    private function drawBackground(Renderer $renderer): void
    {
        $name = __DIR__.'/images/bg'.$this->levelColor.'.png';
        $renderer->drawImage($name, 0, 0, $this->fieldWidth, $this->fieldHeight);

        foreach ($this->backgroundBlocks as $block) {
            $block->draw($renderer);
        }
    }

    private function createBackground(): void
    {
        $this->grid = $this->levels[$this->level % count($this->levels)];
        $this->grid = array_merge($this->grid, [$this->grid[0]]);

        $this->backgroundBlocks = [];

        $blockSprite = __DIR__.'/images/block'.($this->level % 4).'.png';
        foreach ($this->grid as $y => $row) {
            if (empty($row)) {
                continue;
            }
            $cols = str_split($row, 1);
            $x = self::LEVEL_X_OFFSET;
            foreach ($cols as $charBlock) {
                if ($charBlock !== ' ') {
                    $block = new Block(
                        new \SDL_Point($x, $y * self::GRID_BLOCK_SIZE),
                        self::GRID_BLOCK_SIZE,
                        self::GRID_BLOCK_SIZE
                    );
                    $block->setImage($blockSprite);
                    $this->backgroundBlocks[] = $block;
                }
                $x += self::GRID_BLOCK_SIZE;
            }
        }
    }

    private function createEnemies(): void
    {
        $this->bolts = [];
        $this->enemies = [];
        $this->pops = [];
        $this->orbs = [];

        $enemiesCount = 10 + $this->level;
        $strongEnemiesCount = 1 + (int)($this->level / 1.5);
        $weakEnemiesCount = $enemiesCount - $strongEnemiesCount;
        $this->pendingEnemies = array_merge(
            array_fill(0, $strongEnemiesCount, Robot::TYPE_AGGRESSIVE),
            array_fill($strongEnemiesCount, $weakEnemiesCount, Robot::TYPE_NORMAL)
        );
        shuffle($this->pendingEnemies);
    }
}
