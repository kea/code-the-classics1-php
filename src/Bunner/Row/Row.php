<?php

declare(strict_types=1);

namespace Bunner\Row;

use Bunner\Game;
use PhpGame\Anchor;
use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\Sprite;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

abstract class Row implements DrawableInterface
{
    protected const ROW_HEIGHT = 40.0;
    protected const ROW_WIDTH = 480.0;
    protected TextureRepository $textureRepository;
    protected Sprite $sprite;
    protected string $textureName = 'grass%d.png';
    protected Row $previous;
    protected int $index = 0;
    protected float $dx = 0;

    public function __construct(TextureRepository $textureRepository, int $index, ?Row $previous = null)
    {
        $y = is_null($previous) ? Game::HEIGHT - 20 : $previous->sprite->getPosition()->y - self::ROW_HEIGHT;
        echo $y."\n";
        $this->index = $index;
        $this->textureRepository = $textureRepository;
        $this->textureName = sprintf($this->textureName, $index);
        $this->sprite = new Sprite($textureRepository[$this->textureName], 0, $y, Anchor::LeftBottom());
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->render($renderer);
    }

    public function update(float $deltaTime): void
    {
        $speed = new Vector2Float(0, $deltaTime * 60);
        $this->sprite->setPosition($this->sprite->getPosition()->add($speed));
    }

    abstract public function nextRow(): Row;
}
