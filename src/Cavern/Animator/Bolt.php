<?php

declare(strict_types=1);

namespace Cavern\Animator;

use PhpGame\Animator;
use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Bolt extends Animator
{
    protected array $acceptedParams = ['directionX', 'timer'];

    public function __construct(TextureRepository $textureRepository, Sprite $sprite = null, string $defaultImage = 'bolt00.png')
    {
        parent::__construct($textureRepository, $sprite, $defaultImage);
    }

    public function update(float $deltaTime): void
    {
        $directionIdx = $this->getFloat('directionX') > 0 ? "1" : "0";
        $animFrame = floor($this->getFloat('timer') / 4) % 2;
        $image = 'bolt'.$directionIdx.$animFrame;
        $this->image = $image.'.png';
        parent::update($deltaTime);
    }
}
