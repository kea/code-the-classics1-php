<?php

namespace Bunner;

class Rail extends Row
{
    protected string $textureName = 'rail%d.png';

    public function nextRow(): Row
    {
        if ($this->index < 3) {
            return new self($this->textureRepository, $this->index + 1, $this);
        }

        $nextPossibleClasses = [Road::class, Water::class];
        $nextClass = $nextPossibleClasses[array_rand($nextPossibleClasses)];

        return new $nextClass($this->textureRepository, 0, $this);
    }
}