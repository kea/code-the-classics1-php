<?php

namespace Cavern;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;

class Block extends ColliderActor implements DrawableInterface
{
    private string $image;

    public function setImage(string $blockSprite): void
    {
        $this->image = $blockSprite;
    }

    public function update(float $deltaTime): void
    {
        /** @todo remove update */
        /* ehm drawable nor updatable => wrong extract interface!!! */
    }

    public function draw(Renderer $renderer): void
    {
        $renderer->drawImage($this->image, $this->position->x, $this->position->y);
    }
}
