<?php

namespace Myriapod\Explosion;

use PhpGame\Anchor;
use PhpGame\Animation;
use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\Sprite;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

class Explosion extends Animation implements DrawableInterface
{
    public const ROCK = 0;
    public const POD = 1;
    public const ENEMY = 2;
    protected Sprite $sprite;

    public function __construct(TextureRepository $textureRepository, Vector2Float $position, int $type)
    {
        $textures = [];
        for ($i = 0; $i < 8; ++$i) {
            $textures[] = $textureRepository['exp'.$type.$i.'.png'];
        }
        $this->sprite = new Sprite($textureRepository['blank.png'], $position->x + 16, $position->y + 16, Anchor::CenterCenter());

        parent::__construct($textures, 15, false);
        $this->startAnimation();
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->updateTexture($this->getCurrentFrame());
        $this->sprite->draw($renderer);
    }

    public function getPosition(): Vector2Float
    {
        return $this->sprite->getPosition();
    }
}
