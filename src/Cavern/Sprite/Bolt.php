<?php

declare(strict_types=1);

namespace Cavern\Sprite;

use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Bolt extends Sprite
{
    private string $image = 'bolt00';
    private TextureRepository $textureRepository;

    public function __construct(TextureRepository $textureRepository)
    {
        $fullPath = __DIR__.'/../images/'.$this->image.'.png';
        parent::__construct($textureRepository[$fullPath]);
        $this->textureRepository = $textureRepository;
    }

    public function updateImage(float $timer, float $directionX): void
    {
        $directionIdx = $directionX > 0 ? "1" : "0";
        $animFrame = floor($timer / 4) % 2;
        $image = 'bolt'.$directionIdx.$animFrame;
        $this->image = __DIR__.'/../images/'.$image.'.png';
        $this->updateTexture($this->textureRepository[$this->image]);
    }
}
