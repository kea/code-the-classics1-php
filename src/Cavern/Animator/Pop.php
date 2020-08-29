<?php

declare(strict_types=1);

namespace Cavern\Animator;

use PhpGame\Anchor;
use PhpGame\Animator;
use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Pop extends Animator
{
    protected array $acceptedParams = ['timer', 'type'];

    public function __construct(TextureRepository $textureRepository, Sprite $sprite, string $defaultImage = 'pop00.png')
    {
        $sprite->setAnchor(Anchor::CenterBottom());
        parent::__construct($textureRepository, $sprite, $defaultImage);
    }

    public function update(float $deltaTime): void
    {
        $timer = $this->getFloat('timer');
        $type = (int)$this->getFloat('type');
        $frame = floor($timer * 30) < 5 ? floor($timer * 30) : 4;
        $image = "pop".$type.$frame;
        $this->image = $image.'.png';

        parent::update($deltaTime);
    }
}
