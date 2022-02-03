<?php

namespace Cavern;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;

class Block extends ColliderActor implements DrawableInterface
{
    public function draw(Renderer $renderer): void
    {
        $this->sprite->draw($renderer);
    }
}
