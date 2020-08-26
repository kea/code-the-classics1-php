<?php

declare(strict_types=1);

namespace Cavern\Sprite;

use PhpGame\Anchor;
use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Pop extends Sprite
{
    private string $image = 'pop00.png';
    private TextureRepository $textureRepository;

    public function __construct(TextureRepository $textureRepository)
    {
        parent::__construct($textureRepository[$this->image]);
        $this->textureRepository = $textureRepository;
        $this->setAnchor(Anchor::CenterBottom());
    }

    public function updateImage(float $timer, int $type): void
    {
        $frame = floor($timer * 30) < 5 ? floor($timer * 30) : 4;
        $image = "pop".$type.$frame;
        $this->image = $image.'.png';
        $this->updateTexture($this->textureRepository[$this->image]);
    }
}
