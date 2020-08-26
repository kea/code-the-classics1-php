<?php

declare(strict_types=1);

namespace Cavern\Sprite;

use PhpGame\Anchor;
use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Robot extends Sprite
{
    private string $image = 'robot000';
    private TextureRepository $textureRepository;

    public function __construct(TextureRepository $textureRepository)
    {
        $fullPath = __DIR__.'/../images/'.$this->image.'.png';
        parent::__construct($textureRepository[$fullPath]);
        $this->textureRepository = $textureRepository;
        $this->setAnchor(Anchor::CenterBottom());
    }

    public function updateImage(int $directionX, int $type, float $fireTimer, float $lifeTimer): void
    {
        $image = $this->chooseImage($directionX, $type, $fireTimer, $lifeTimer);
        $this->image = __DIR__.'/../images/'.$image.'.png';
        $this->updateTexture($this->textureRepository[$this->image]);
    }

    private function chooseImage(int $directionX, int $type, float $fireTimer, float $lifeTimer): string
    {
        $directionIdx = $directionX > 0 ? "1" : "0";
        $image = "robot".$type.$directionIdx;
        if ($fireTimer < 12 / 60) {
            return $image.(5 + floor($fireTimer / (4 / 60)));
        }

        return $image.(1 + (floor($lifeTimer / (4 / 60)) % 4));
    }
}