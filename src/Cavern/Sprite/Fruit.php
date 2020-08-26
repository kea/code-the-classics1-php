<?php

declare(strict_types=1);

namespace Cavern\Sprite;

use PhpGame\Anchor;
use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Fruit extends Sprite
{
    private string $image = 'fruit00.png';
    private TextureRepository $textureRepository;

    public function __construct(TextureRepository $textureRepository)
    {
        parent::__construct($textureRepository[$this->image]);
        $this->textureRepository = $textureRepository;
        $this->setAnchor(Anchor::CenterBottom());
    }

    public function updateImage(float $timer, int $type): void
    {
        if ($timer < 0) {
            return;
        }

        $frames = [0, 1, 2, 1];
        $animFrame = $frames[floor($timer * 10) % 4];
        $image = 'fruit'.$type.$animFrame;

        $this->image = $image.'.png';
        $this->updateTexture($this->textureRepository[$this->image]);
    }
}