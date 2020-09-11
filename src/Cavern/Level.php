<?php

namespace Cavern;

use PhpGame\Anchor;
use PhpGame\SDL\Renderer;
use PhpGame\Sprite;
use PhpGame\TextureRepository;
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
    private TextureRepository $textureRepository;

    public function __construct(int $fieldWidth, int $fieldHeight, TextureRepository $textureRepository)
    {
        $this->levels = json_decode(file_get_contents(__DIR__.'/levels/levels.json'), true, 512, JSON_THROW_ON_ERROR);
        $this->fieldWidth = $fieldWidth;
        $this->fieldHeight = $fieldHeight;
        $this->textureRepository = $textureRepository;
    }

    public function blockStartAt(float $pos): bool
    {
        return (int)$pos % self::GRID_BLOCK_SIZE === 0;
    }

    public function blockEndAt(float $pos): bool
    {
        return (int)$pos % self::GRID_BLOCK_SIZE === self::GRID_BLOCK_SIZE - 1;
    }

    public function draw(Renderer $renderer): void
    {
        $name = __DIR__.'/images/bg'.$this->levelColor.'.png';
        $renderer->drawImage($name, 0, 0);

        foreach ($this->backgroundBlocks as $block) {
            $block->draw($renderer);
        }
    }

    public function buildNextLevel(): void
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

        $blockSpriteName = 'block'.($this->level % 4).'.png';
        $blockSpriteTexture = $this->textureRepository[$blockSpriteName];

        foreach ($this->grid as $y => $row) {
            if (empty($row)) {
                continue;
            }
            $cols = str_split($row, 1);
            $x = self::LEVEL_X_OFFSET;
            foreach ($cols as $charBlock) {
                if ($charBlock !== ' ') {
                    $sprite = new Sprite($blockSpriteTexture);
                    $sprite->setAnchor(Anchor::LeftTop());
                    $block = new Block($sprite);
                    $block->setPosition(new Vector2Float($x, $y * self::GRID_BLOCK_SIZE));
                    $this->backgroundBlocks[] = $block;
                }
                $x += self::GRID_BLOCK_SIZE;
            }
        }
    }

    public function blockAt(float $x, float $y): bool
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
