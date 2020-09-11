<?php

namespace PhpGame;

interface SoundEmitterInterface
{
    public function setSoundManager(SoundManager $soundManager);
    public function playSound(string $sound);
}
