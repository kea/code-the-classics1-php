<?php

declare(strict_types=1);

namespace Cavern\Animator;

use PhpGame\Anchor;
use PhpGame\Animator;
use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Player extends Animator
{
    protected array $acceptedParams = ['dx', 'directionX', 'timer', 'hurtTimer', 'fireTimer', 'health'];

    public function __construct(TextureRepository $textureRepository, Sprite $sprite =  null, string $defaultImage = 'bolt00.png')
    {
        parent::__construct($textureRepository, $sprite, $defaultImage);
        $this->sprite->setAnchor(Anchor::CenterBottom());
    }

    public function update(float $deltaTime): void
    {
        $dx = $this->getFloat('dx');
        $directionX = $this->getFloat('directionX');
        $timer = $this->getFloat('timer');
        $hurtTimer = $this->getFloat('hurtTimer');
        $fireTimer = $this->getFloat('fireTimer');
        $health = $this->getInt('health');

        $image = $this->chooseImage($dx, $directionX, $timer, $hurtTimer, $fireTimer, $health);
        $this->image = $image.'.png';
        parent::update($deltaTime);
    }

    private function chooseImage(float $dx, float $directionX, float $timer, float $hurtTimer, float $fireTimer, int $health): string
    {
        if ($hurtTimer > 0 && round($hurtTimer * 60) % 2 !== 1) {
            return "blank";
        }

        $dirIndex = $directionX > 0 ? "1" : "0";
        if ($hurtTimer > 1.7) {
            return $health > 0 ? "recoil".$dirIndex : "fall".((int)($timer * 12.5) % 2);
        }

        if ($fireTimer > .0) {
            return "blow".$dirIndex;
        }

        if ($dx === 0.0) {
            return "still";
        }

        return "run".$dirIndex.((int)($timer * 7.5) % 4);
    }
}