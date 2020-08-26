<?php

declare(strict_types=1);

namespace Cavern\Sprite;

use PhpGame\Anchor;
use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Player extends Sprite
{
    private string $image = 'still';
    private TextureRepository $textureRepository;

    public function __construct(TextureRepository $textureRepository)
    {
        $this->image = __DIR__.'/../images/'.$this->image.'.png';
        parent::__construct($textureRepository[$this->image]);
        $this->textureRepository = $textureRepository;
        $this->setAnchor(Anchor::CenterBottom());
    }

    public function updateImage(float $dx, float $directionX, float $timer, float $hurtTimer, float $fireTimer, int $health): void
    {
        $image = $this->chooseImage($dx, $directionX, $timer, $hurtTimer, $fireTimer, $health);
        $this->image = __DIR__.'/../images/'.$image.'.png';
        $this->updateTexture($this->textureRepository[$this->image]);
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