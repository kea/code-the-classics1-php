<?php

declare(strict_types=1);

namespace Cavern\Sprite;

use Cavern\Robot;
use PhpGame\Sprite;
use PhpGame\TextureRepository;

class Orb extends Sprite
{
    private string $image = 'orb0';
    private TextureRepository $textureRepository;

    public function __construct(TextureRepository $textureRepository)
    {
        $fullPath = __DIR__.'/../images/'.$this->image.'.png';
        parent::__construct($textureRepository[$fullPath]);
        $this->textureRepository = $textureRepository;
    }

    public function updateImage(float $timer, int $trappedEnemyType): void
    {
        if ($timer < 0.15) {
            $image = "orb".(floor($timer * 20) % 3);
        } elseif ($trappedEnemyType !== Robot::TYPE_NONE) {
            $image = "trap".$trappedEnemyType.(floor($timer * 15) % 8);
        } else {
            $image = "orb".round(3 + (floor(($timer - 0.15) * 7.5) % 4));
        }

        $this->image = __DIR__.'/../images/'.$image.'.png';
        $this->updateTexture($this->textureRepository[$this->image]);
    }
}
