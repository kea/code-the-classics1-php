<?php

declare(strict_types=1);

namespace Cavern\Animator;

use PhpGame\Anchor;
use PhpGame\Animator;
use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Fruit extends Animator
{
    protected array $acceptedParams = ['timeToLive', 'type'];

    public function __construct(TextureRepository $textureRepository, Sprite $sprite = null, string $defaultImage = 'fruit00.png')
    {
        parent::__construct($textureRepository, $sprite, $defaultImage);
        $this->sprite->setAnchor(Anchor::CenterBottom());
    }

    public function update(float $deltaTime): void
    {
        $timer = $this->getFloat('timeToLive');
        $type = $this->getInt('type');

        if ($timer < 0) {
            return;
        }

        $frames = [0, 1, 2, 1];
        $animFrame = $frames[floor($timer * 10) % 4];
        $image = 'fruit'.$type.$animFrame;

        $this->image = $image.'.png';
        parent::update($deltaTime);
    }
}
