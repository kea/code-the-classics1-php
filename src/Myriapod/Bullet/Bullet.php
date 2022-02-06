<?php

namespace Myriapod\Bullet;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\Sprite;
use PhpGame\TextureRepository;
use PhpGame\TimeUpdatableInterface;
use PhpGame\Vector2Float;

class Bullet implements DrawableInterface, TimeUpdatableInterface
{
    protected Sprite $sprite;

    public function __construct(TextureRepository $textureRepository, Vector2Float $position)
    {
        $this->sprite = new Sprite($textureRepository['bullet.png'], $position->x, $position->y);
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->draw($renderer);
    }

    public function update(float $deltaTime): void
    {
        $this->sprite->setPosition(
            $this->sprite->getPosition()->add(new Vector2Float(0, -24 * 60 * $deltaTime))
        );
    }

    public function getPosition(): Vector2Float
    {
        return $this->sprite->getPosition();
    }
}
