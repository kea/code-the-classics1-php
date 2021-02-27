<?php

declare(strict_types=1);

namespace Bunner\Obstacle;

use PhpGame\Anchor;
use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\Sprite;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

class Hedge implements DrawableInterface
{
    protected string $textureName = 'bush%d%d.png';
    private Sprite $sprite;

    public function __construct(TextureRepository $textureRepository, int $bushPosition, int $rowPosition, Vector2Float $position)
    {
        $image = sprintf($this->textureName, $bushPosition, $rowPosition);
        $this->sprite = new Sprite($textureRepository[$image], $position->x, $position->y, Anchor::CenterBottom());
    }

    public function update(float $deltaTime): void
    {
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->render($renderer);
    }

    public function setY(float $y): void
    {
        $position = $this->sprite->getPosition();
        $position->y = $y;
        $this->sprite->setPosition($position);
    }
}