<?php

namespace PhpGame;

use PhpGame\SDL\Renderer;
use PhpGame\SDL\Texture;

class SpriteGrid
{
    /** @var array<int, array<int, Sprite|null>> */
    private array $grid;
    /** @var SDL\Texture[] */
    private array $textures;
    private int $cellWidth = 70;
    private int $cellHeight = 70;

    /**
     * SpriteGrid constructor.
     * @param array<string>                  $spriteNames
     * @param array<int, array<int, string>> $grid
     * @param Renderer                       $renderer
     * @throws AssetLoadException
     */
    public function __construct(array $spriteNames, array $grid, Renderer $renderer)
    {
        $this->loadTextures($spriteNames, $renderer);
        $this->setGrid($grid);
    }

    /**
     * @param Renderer $renderer
     */
    public function render(Renderer $renderer): void
    {
        foreach ($this->grid as $row) {
            foreach ($row as $cell) {
                if ($cell instanceof Sprite) {
                    $cell->draw($renderer);
                }
            }
        }
    }

    /**
     * @param array<int, array<int, string>> $grid
     */
    public function setGrid(array $grid): void
    {
        $anchorLeftTop = new Anchor(Anchor::LEFT, Anchor::TOP);
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
                    $x * $this->cellWidth,
                    $y * $this->cellHeight
                );
                $this->grid[$x][$y]->setAnchor($anchorLeftTop);
            }
        }
    }

    /**
     * @param array<string, string> $texturesMap
     * @param Renderer              $renderer
     */
    private function loadTextures(array $texturesMap, Renderer $renderer): void
    {
        $this->textures = [];
        foreach ($texturesMap as $textureName => $spriteFile) {
            $this->textures[$textureName] = Texture::loadFromFile($spriteFile, $renderer);
        }
    }
}
