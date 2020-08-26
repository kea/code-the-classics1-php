<?php

declare(strict_types=1);

namespace Cavern\Sprite;

use PhpGame\Anchor;
use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Fruit extends Sprite
{
    private string $image = 'fruit00';
    private TextureRepository $textureRepository;

    public function __construct(TextureRepository $textureRepository)
    {
        $fullPath = __DIR__.'/../images/'.$this->image.'.png';
        parent::__construct($textureRepository[$fullPath]);
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

        $this->image = __DIR__.'/../images/'.$image.'.png';
        $this->updateTexture($this->textureRepository[$this->image]);
    }
}