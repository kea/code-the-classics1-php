<?php

namespace PhpGame;

class SpriteGrid
{
    /** @var array<int, array<int, Sprite>> */
    private array $grid;
    /** @var SDL\Texture[] */
    private array $textures;
    private int $cellWidth = 70;
    private int $cellHeight = 70;

    /**
     * SpriteGrid constructor.
     * @param array<string>                  $spriteNames
     * @param array<int, array<int, string>> $grid
     * @param resource                       $renderer
     * @throws AssetLoadException
     */
    public function __construct(array $spriteNames, array $grid, $renderer)
    {
        $this->loadTextures($spriteNames, $renderer);
        $this->setGrid($grid);
    }

    /**
     * @param resource $renderer
     */
    public function render($renderer): void
    {
        foreach ($this->grid as $row) {
            foreach ($row as $cell) {
                if ($cell instanceof Sprite) {
                    $cell->render($renderer);
                }
            }
        }
    }

    /**
     * @param array<int, array<int, string>> $grid
     */
    public function setGrid(array $grid): void
    {
        $this->grid = [];
        foreach ($grid as $y => $row) {
            foreach ($row as $x => $textureName) {
                if (!isset($this->grid[$x])) {
                    $this->grid[$x] = [];
                }
                if (empty($textureName)) {
                    $this->grid[$x][$y] = null;
                    continue;
                }

                $this->grid[$x][$y] = new Sprite(
                    $this->textures[$textureName],
                    $this->cellWidth,
                    $this->cellHeight,
                    $x * $this->cellWidth,
                    $y * $this->cellHeight
                );
            }
        }
    }

    /**
     * @param array<string, string> $texturesMap
     * @param resource              $renderer
     * @throws AssetLoadException
     */
    private function loadTextures(array $texturesMap, $renderer): void
    {
        $this->textures = [];
        foreach ($texturesMap as $textureName => $spriteFile) {
            $this->textures[$textureName] = SDL\Texture::loadFromFile($spriteFile, $renderer);
        }
    }
}
