<?php

declare(strict_types=1);

namespace PhpGame;

trait SoundEmitterTrait
{
    private ?SoundManager $soundManager = null;

    public function setSoundManager(SoundManager $soundManager): void
    {
        $this->soundManager = $soundManager;
    }

    public function playSound(string $sound): void
    {
        if ($this->soundManager === null) {
            throw new \RuntimeException("Sound manager required");
        }
        $this->soundManager->playSound($sound);
    }
}
