<?php

declare(strict_types=1);

namespace Bunner\Row;

use Bunner\Obstacle\Log;
use Bunner\Obstacle\Mover;
use Bunner\Player\Bunner;
use Bunner\Player\PlayerState;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

class Water extends ActiveRow
{
    protected string $textureName = 'water%d.png';

    public function __construct(TextureRepository $textureRepository, int $index, ?Row $previous = null)
    {
        if (($previous === null) || ($previous->dx === .0)) {
            $dxs = [-2, -1, 1, 2];
        } elseif ($previous->dx > 0) {
            $dxs = [-2, -1];
        } else {
            $dxs = [1, 2];
        }
        /** @psalm-suppress PossiblyUndefinedArrayOffset */
        $this->dx = $dxs[array_rand($dxs)];
        parent::__construct($textureRepository, $index, $previous);
    }

    public function nextRow(): Row
    {
        $random = random_int(1, 100);
        if (($this->index === 7) || ($this->index >= 1 && $random < 50)) {
            return new Dirt($this->textureRepository, random_int(4, 6), $this);
        }

        return new self($this->textureRepository, $this->index + 1, $this);
    }

    public function update(float $deltaTime): void
    {
        parent::update($deltaTime);

//        foreach ($this->children as $i => $child) {
//            if ($bunner && $this->sprite->y == $bunner->sprite->y && child == collide($bunner->sprite->x, -4) {
//                $y = 2
//            } else {
//                $y = 0
//            }
//        }
    }

    public function push(): Vector2Float
    {
        return $this->scrollSpeed()->add(new Vector2Float($this->dx * Mover::SPEED_PIXEL_PER_SECOND, .0));
    }

    public function playLandedSound(): void
    {
        $this->playSound("log0.wav");
    }

    public function checkCollision(Bunner $player): string
    {
        if ($this->collide($player->getX(), -4)) {
            return PlayerState::ALIVE;
        }

        $this->playSound("splash0.wav");

        return PlayerState::SPLASH;
    }

    protected function createChild(Vector2Float $position): Mover
    {
        return new Log($this->textureRepository, $position,  $this->dx);
    }
}
