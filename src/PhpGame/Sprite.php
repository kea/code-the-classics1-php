<?php

namespace PhpGame;

use PhpGame\SDL\Renderer;
use PhpGame\SDL\Texture;

class Sprite
{
    private Texture $texture;
    private \SDL_Rect $boundedRect;
    private \SDL_Point $pivot;
    private \SDL_Point $position;

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
        $this->boundedRect = new \SDL_Rect($x, $y, $width, $height);
    }

    public static function fromImage(string $path, int $width, int $height, Renderer $renderer): self
    {
        $texture = Texture::loadFromFile($path, $renderer);

        return new self($texture, $width, $height);
    }

    /**
     * @return Vector2Int
     */
    public function getPosition(): Vector2Int
    {
        return new Vector2Int($this->boundedRect->x, $this->boundedRect->y);
    }

    /**
     * @param int $x
     * @param int $y
     */
    public function setPosition(int $x, int $y): void
    {
        $this->boundedRect->x = $x;
        $this->boundedRect->y = $y;
    }

    /**
     * @param int $x
     */
    public function setPositionX(int $x): void
    {
        $this->boundedRect->x = $x;
    }

    /**
     * @param int $y
     */
    public function setPositionY(int $y): void
    {
        $this->boundedRect->y = $y;
    }

    public function render(Renderer $renderer): void
    {
        $renderer->copy($this->texture, null, $this->boundedRect);
    }
}
