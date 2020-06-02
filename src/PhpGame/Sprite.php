<?php

namespace PhpGame;

use PhpGame\SDL\Texture;

class Sprite
{
    private Texture $texture;
    private Vector2Int $size;
    private Vector2Int $position;

    /**
     * Sprite constructor.
     * @param Texture $texture
     * @param int     $width
     * @param int     $height
     * @param int     $x
     * @param int     $y
     */
    public function __construct(Texture $texture, int $width, int $height, int $x = 0, int $y = 0)
    {
        $this->texture = $texture;
        $this->size = new Vector2Int($width, $height);
        $this->position = new Vector2Int($x, $y);
    }

    /**
     * @return Vector2Int
     */
    public function getPosition(): Vector2Int
    {
        return $this->position;
    }

    /**
     * @param int $x
     * @param int $y
     */
    public function setPosition(int $x, int $y): void
    {
        $this->position = new Vector2Int($x, $y);
    }

    /**
     * @param int $x
     */
    public function setPositionX(int $x): void
    {
        $this->position = new Vector2Int($x, $this->position->y());
    }

    /**
     * @param int $y
     */
    public function setPositionY(int $y): void
    {
        $this->position = new Vector2Int($this->position->x(), $y);
    }

    private function getBoundedRect(): \SDL_Rect
    {
        return new \SDL_Rect($this->position->x(), $this->position->y(), $this->size->x(), $this->size->y());
    }

    /**
     * @param resource $renderer
     */
    public function render($renderer): void
    {
        if (\SDL_RenderCopy($renderer, $this->texture->getContent(), NULL, $this->getBoundedRect()) !== 0) {
            echo \SDL_GetError(), PHP_EOL;
        }
    }
}
