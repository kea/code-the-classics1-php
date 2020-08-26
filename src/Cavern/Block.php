<?php

namespace Cavern;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;

class Block extends ColliderActor implements DrawableInterface
{
    public function update(float $deltaTime): void
    {
        /** @todo remove update */
        /* ehm drawable nor updatable => wrong extract interface!!! */
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->render($renderer);
    }
}
