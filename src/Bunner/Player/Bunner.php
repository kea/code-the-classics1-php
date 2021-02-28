<?php

namespace Bunner\Player;

use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Bunner
{
    private Sprite $sprite;

    public function __construct(TextureRepository $textureRepository)
    {
        $this->sprite = new Sprite($textureRepository['sit0.png']);
    }

    public function getY(): float
    {
        return $this->sprite->getPosition()->y;
    }

    public function getX(): float
    {
        return $this->sprite->getPosition()->x;
    }

    public function getSprite(): Sprite
    {
        return $this->sprite;
    }
}