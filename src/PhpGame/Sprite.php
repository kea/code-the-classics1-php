<?php

namespace PhpGame;

use PhpGame\SDL\Renderer;
use PhpGame\SDL\Texture;

class Sprite implements DrawableInterface
{
    private Texture $texture;
    protected \SDL_Rect $boundedRect;
    protected Transform $transform;
    private Anchor $anchor;

    /**
     * Sprite constructor.
     * @param Texture     $texture
     * @param float       $x
     * @param float       $y
     * @param Anchor|null $anchor
     */
    public function __construct(Texture $texture, float $x = 0, float $y = 0, ?Anchor $anchor = null)
    {
        $this->texture = $texture;
        $this->anchor = $anchor ?? Anchor::CenterCenter();
        $this->transform = new Transform(new Vector2Float($x, $y));
        $this->updateBoundedRect();
    }

    public static function fromImage(string $path, Renderer $renderer): self
    {
        $texture = Texture::loadFromFile($path, $renderer);

        return new self($texture);
    }

    public function getPosition(): Vector2Float
    {
        return $this->transform->getPosition();
    }

    public function setPosition(Vector2Float $position): void
    {
        $this->transform->setPosition($position);
        $this->updateBoundedRect();
    }

    public function draw(Renderer $renderer): void
    {
        $renderer->drawTexture($this->texture, $this->boundedRect);
//        $renderer->setDrawColor([90, 96, 93, 0]);
//        $renderer->drawRectangle($this->boundedRect);
    }

    private function updateBoundedRect(): void
    {
        $width = $this->texture->getWidth() * $this->transform->getScale()->x;
        $height = $this->texture->getHeight() * $this->transform->getScale()->y;

        $this->boundedRect = $this->anchor->getBoundedRect(
            $this->transform->getPosition()->x,
            $this->transform->getPosition()->y,
            $width,
            $height
        );
    }

    public function setAnchor(Anchor $anchor): void
    {
        $this->anchor = $anchor;
        $this->updateBoundedRect();
    }

    public function top(): float
    {
        return $this->boundedRect->y;
    }

    public function bottom(): float
    {
        return $this->boundedRect->y + $this->boundedRect->h;
    }

    public function getBoundedRect(): \SDL_Rect
    {
        return $this->boundedRect;
    }

    public function updateTexture(Texture $texture): void
    {
        $this->texture = $texture;
        $this->updateBoundedRect();
    }

    public function __clone()
    {
        $this->boundedRect = clone $this->boundedRect;
        $this->transform = clone $this->transform;
        $this->anchor = clone $this->anchor;
    }
}

