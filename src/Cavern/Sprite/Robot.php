<?php

declare(strict_types=1);

namespace Cavern\Sprite;

use PhpGame\Anchor;
use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Robot extends Sprite
{
    private string $image = 'robot000.png';
    private TextureRepository $textureRepository;

    public function __construct(TextureRepository $textureRepository)
    {
        parent::__construct($textureRepository[$this->image]);
        $this->textureRepository = $textureRepository;
        $this->setAnchor(Anchor::CenterBottom());
    }

    public function updateImage(int $directionX, int $type, float $fireTimer, float $lifeTimer): void
    {
        $image = $this->chooseImage($directionX, $type, $fireTimer, $lifeTimer);
        $this->image = $image.'.png';
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