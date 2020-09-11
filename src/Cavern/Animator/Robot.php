<?php

declare(strict_types=1);

namespace Cavern\Animator;

use PhpGame\Anchor;
use PhpGame\Animator;
use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Robot extends Animator
{
    protected array $acceptedParams = ['directionX', 'type', 'fireTimer', 'lifeTimer'];

    public function __construct(TextureRepository $textureRepository, Sprite $sprite = null, string $defaultImage = 'robot000.png')
    {
        parent::__construct($textureRepository, $sprite, $defaultImage);
        $this->sprite->setAnchor(Anchor::CenterBottom());
    }

    public function update(float $deltaTime): void
    {
        $directionX = $this->getFloat('directionX');
        $type = $this->getInt('type');
        $fireTimer = $this->getFloat('fireTimer');
        $lifeTimer = $this->getFloat('lifeTimer');

        $directionIdx = $directionX > 0 ? "1" : "0";
        $image = "robot".$type.$directionIdx;

        $frameSpeed = 4 / 60;
        $nthFireFrame = floor($fireTimer / $frameSpeed);
        $nthLifeFrame = floor($lifeTimer / $frameSpeed);
        $image .= $fireTimer < 3 * $frameSpeed ? (5 + $nthFireFrame) : (1 + $nthLifeFrame % 4);

        $this->image = $image.'.png';
        parent::update($deltaTime);
    }
}