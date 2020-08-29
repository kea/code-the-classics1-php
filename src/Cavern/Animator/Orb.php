<?php

declare(strict_types=1);

namespace Cavern\Animator;

use Cavern\Robot;
use PhpGame\Animator;
use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Orb extends Animator
{
    protected array $acceptedParams = ['timer', 'trappedEnemyType'];

    public function __construct(TextureRepository $textureRepository, Sprite $sprite, string $defaultImage = 'orb0.png')
    {
        parent::__construct($textureRepository, $sprite, $defaultImage);
    }

    public function update(float $deltaTime): void
    {
        $timer = $this->getFloat('timer');
        $trappedEnemyType = $this->getInt('trappedEnemyType');
        if ($timer < 0.15) {
            $image = "orb".(floor($timer * 20) % 3);
        } elseif ($trappedEnemyType !== Robot::TYPE_NONE) {
            $image = "trap".$trappedEnemyType.(floor($timer * 15) % 8);
        } else {
            $image = "orb".round(3 + (floor(($timer - 0.15) * 7.5) % 4));
        }

        $this->image = $image.'.png';
        parent::update($deltaTime);
    }
}
