<?php

namespace Bunner;

class Water extends Row
{
    protected string $textureName = 'water%d.png';

    public function nextRow(): Row
    {
        $random = random_int(1, 100);
        if (($this->index === 7) || ($this->index >= 1 && $random < 50)) {
            return new Dirt($this->textureRepository, random_int(4, 6), $this);
        }

        return new self($this->textureRepository, $this->index + 1, $this);

    }
}
