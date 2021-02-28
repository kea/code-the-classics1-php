<?php

declare(strict_types=1);

namespace Bunner\Obstacle;

use PhpGame\Anchor;
use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\Sprite;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

abstract class Mover implements DrawableInterface
{
    private const SPEED_PIXEL_PER_SECOND = 60;
    private Sprite $sprite;
    protected Vector2Float $speed;

    public function __construct(TextureRepository $textureRepository, Vector2Float $position, float $dx)
    {
        $this->speed = new Vector2Float($dx * self::SPEED_PIXEL_PER_SECOND, 0);
        $image = $this->getRandomSpriteName();
        $this->sprite = new Sprite($textureRepository[$image], $position->x, $position->y, Anchor::CenterBottom());
    }

    abstract protected function getRandomSpriteName(): string;

    public function update(float $deltaTime): void
    {
        $position = $this->sprite->getPosition();
        $speed = clone $this->speed;
        $this->sprite->setPosition($position->add($speed->multiplyFloat($deltaTime)));
    }

    public function getX(): float
    {
        return $this->sprite->getPosition()->x;
    }

    public function setY(float $y): void
    {
        $position = $this->sprite->getPosition();
        $position->y = $y;
        $this->sprite->setPosition($position);
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->render($renderer);
    }

    public function getDx()
    {
        return $this->speed->x;
    }

    public function getSprite(): Sprite
    {
        return $this->sprite;
    }
}
