<?php

declare(strict_types=1);

namespace Bunner\Obstacle;

use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;

class Car extends Mover implements SoundEmitterInterface
{
    use SoundEmitterTrait;
    public const SOUND_ZOOM = 'zoom';
    public const SOUND_HONK = 'honk';
    private array $played;
    // [howManySounds, played]
    private array $sounds = [
        self::SOUND_ZOOM => [6, false],
        self::SOUND_HONK => [4, false]
    ];

    public function getRandomSpriteName(): string
    {
        return "car".random_int(0, 3).($this->speed->x < 0 ? "0" : "1").'.png';
    }

    public function playSound(string $sound): void
    {
        if (!isset($this->sounds[$sound]) || $this->sounds[$sound][1]) {
            return;
        }
        if ($this->soundManager === null) {
            throw new \RuntimeException("Sound manager required");
        }
        $this->soundManager->playSound($sound.random_int(0, $this->sounds[$sound][0] - 1));
        $this->sounds[$sound][1] = true;
    }
}