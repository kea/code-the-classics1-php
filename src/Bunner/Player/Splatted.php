<?php

namespace Bunner\Player;

use Bunner\RectangleBounded;
use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\SDL\Texture;
use PhpGame\Sprite;

class Splatted implements DrawableInterface, RectangleBounded
{
    private Sprite $sprite;

    public function __construct(Texture $texture, float $x, float $y)
    {
        $this->sprite = new Sprite($texture, $x, $y);
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->draw($renderer);
    }

    public function getBoundedRectangle(): \SDL_Rect
    {
        return $this->sprite->getBoundedRect();
    }

    public function getX(): float
    {
        return $this->sprite->getPosition()->x;
    }

    public function getY(): float
    {
        return $this->sprite->getPosition()->y;
    }

    public function setY(float $y): void
    {
        $position = $this->sprite->getPosition();
        $position->y = $y;
        $this->sprite->setPosition($position);
    }
}