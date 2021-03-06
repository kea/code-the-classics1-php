<?php

namespace Bunner\Row;

class Pavement extends Row
{
    protected string $textureName = 'side%d.png';

    public function nextRow(): Row
    {
        if ($this->index < 2) {
            return new self($this->textureRepository, $this->index + 1, $this);
        }

        return new Road($this->textureRepository, 0, $this);
    }

    public function playLandedSound(): void
    {
        $this->playSound("sidewalk0.wav");
    }
}