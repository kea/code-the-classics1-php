<?php

namespace Bunner;

class Road extends Row
{
    protected string $textureName = 'road%d.png';

    public function nextRow(): Row
    {
        if ($this->index === 0) {
            return new self($this->textureRepository, 1, $this);
        }
        if ($this->index < 5) {
            $random = random_int(1, 100);
            if ($random < 80) {
                return new self($this->textureRepository, $this->index + 1, $this);
            }
            if ($random < 88) {
                return new Grass($this->textureRepository, random_int(0, 6), $this);
            }
            if ($random < 94) {
                return new Rail($this->textureRepository, 0, $this);
            }
            return new Pavement($this->textureRepository, 0, $this);
        }

        $random = random_int(1, 100);
        if ($random < 60) {
            return new Grass($this->textureRepository, random_int(0, 6), $this);
        }
        if ($random < 90) {
            return new Rail($this->textureRepository, 0, $this);
        }
        return new Pavement($this->textureRepository, 0, $this);
    }
}
