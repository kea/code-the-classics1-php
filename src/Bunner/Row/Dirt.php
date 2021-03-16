<?php

declare(strict_types=1);

namespace Bunner\Row;

class Dirt extends Row
{
    protected string $textureName = 'dirt%d.png';

    public function nextRow(): Row
    {
        $nextRowClass = Dirt::class;
        $index = 0;
        if ($this->index > 14) {
            $nextPossibleClasses = [Road::class, Water::class];
            $nextRowClass = $nextPossibleClasses[array_rand($nextPossibleClasses)];
        } elseif ($this->index <= 5) {
            $index = $this->index + 8;
        } elseif ($this->index === 6) {
            $index = 7;
        } elseif ($this->index === 7) {
            $index = 15;
        } else {
            $index = $this->index + 1;
        }

        return new $nextRowClass($this->textureRepository, $index, $this);
    }

    public function playLandedSound(): void
    {
        $this->playSound("dirt0.wav");
    }
}
