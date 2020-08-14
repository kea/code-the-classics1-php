<?php

namespace Cavern;

use PhpGame\SDL\Renderer;
use PhpGame\Vector2Float;

class Level
{
    private const LEVEL_X_OFFSET = 50;
    private const GRID_BLOCK_SIZE = 25;
    private const NUM_COLUMNS = 28;

    private array $levels;
    public int $level = -1;
    private array $backgroundBlocks;
    private array $grid;
    private int $levelColor = -1;
    private int $fieldWidth;
    private int $fieldHeight;

    public function __construct(int $fieldWidth, int $fieldHeight)
    {
        $this->levels = json_decode(file_get_contents(__DIR__.'/levels/levels.json'), true, 512, JSON_THROW_ON_ERROR);
        $this->fieldWidth = $fieldWidth;
        $this->fieldHeight = $fieldHeight;
    }

    public static function blockStartAt(float $pos): bool
    {
        return (int)$pos % self::GRID_BLOCK_SIZE === 0;
    }

    public static function blockEndAt(float $pos): bool
    {
        return (int)$pos % self::GRID_BLOCK_SIZE === self::GRID_BLOCK_SIZE - 1;
    }

    public function draw(Renderer $renderer): void
    {
        $name = __DIR__.'/images/bg'.$this->levelColor.'.png';
        $renderer->drawImage($name, 0, 0, $this->fieldWidth, $this->fieldHeight);

        foreach ($this->backgroundBlocks as $block) {
            $block->draw($renderer);
        }
    }

    public function buildNextLevel()
    {
        $this->levelColor = ($this->levelColor + 1) % 4;
        ++$this->level;
        $this->createBackground();
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
                        new Vector2Float($x, $y * self::GRID_BLOCK_SIZE),
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

    public function blockAt(float $x, float $y)
    {
        $gridX = (int)(($x - self::LEVEL_X_OFFSET) / self::GRID_BLOCK_SIZE);
        $gridY = (int)($y / self::GRID_BLOCK_SIZE);
        if ($gridY > 0 && $gridY < self::NUM_COLUMNS) {
            return !empty($this->grid[$gridY][$gridX]) && $this->grid[$gridY][$gridX] !== ' ';
        }

        return false;
    }

    public function getRobotSpawnX()
    {
        $r = random_int(0, self::NUM_COLUMNS);
        for ($i = 0; $i < self::NUM_COLUMNS; ++$i) {
            $gridX = ($r + $i) % self::NUM_COLUMNS;
            if ($this->grid[0][$gridX] === ' ') {
                return self::GRID_BLOCK_SIZE * $gridX + self::LEVEL_X_OFFSET + 12;
            }
        }

        return WINDOW_WIDTH / 2;
    }
}
