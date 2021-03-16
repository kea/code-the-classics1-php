<?php

declare(strict_types=1);

namespace Bunner\Row;

use Bunner\Game;
use Bunner\Obstacle\Mover;
use Bunner\Player\Bunner;
use Bunner\Player\PlayerState;
use Bunner\RectangleBounded;
use PhpGame\Anchor;
use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;
use PhpGame\Sprite;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

abstract class Row implements DrawableInterface, SoundEmitterInterface
{
    use SoundEmitterTrait;
    protected const ROW_HEIGHT = 40.0;
    protected const ROW_WIDTH = 480.0;
    protected TextureRepository $textureRepository;
    protected Sprite $sprite;
    protected string $textureName = 'grass%d.png';
    protected Row $previous;
    protected int $index = 0;
    protected float $dx = 0;
    /** @var array|RectangleBounded[] */
    protected array $children = [];

    public function __construct(TextureRepository $textureRepository, int $index, ?Row $previous = null)
    {
        $y = is_null($previous) ? Game::HEIGHT + 20: $previous->sprite->getPosition()->y - self::ROW_HEIGHT;
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
        $this->sprite->setPosition($this->sprite->getPosition()->add($this->scrollSpeed()->multiplyFloat($deltaTime)));
    }

    public function push(): Vector2Float
    {
        return $this->scrollSpeed();
    }

    public function checkCollision(Bunner $player): string
    {
        return PlayerState::ALIVE;
    }

    public function collide(float $x, float $margin = 0): ?Mover
    {
        foreach ($this->children as $child) {
            $rect = $child->getBoundedRectangle();
            if (($x >= $rect->x - $margin) &&
                ($x < $rect->x + $rect->w + $margin)) {
                return $child;
            }
        }

        return null;
    }

    abstract public function nextRow(): Row;

    abstract public function playLandedSound(): void;

    protected function scrollSpeed(): Vector2Float
    {
        return new Vector2Float(.0, 60.0);
    }

    public function contains(Vector2Float $center): bool
    {
        //return \SDL_PointInRect(new \SDL_Point($center->x, $center->y), $this->sprite->getBoundedRect());
        $bound = $this->sprite->getBoundedRect();
        return (($center->x >= $bound->x) &&
                ($center->x <= $bound->x + $bound->w) &&
                ($center->y >= $bound->y) &&
                ($center->y <= $bound->y + $bound->h));
    }

    public function allowMovement(float $x): bool
    {
        return $x > 16 && $x < Game::WIDTH - 16;
    }
}
