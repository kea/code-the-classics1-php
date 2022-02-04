<?php

namespace Myriapod\Player;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\TimeUpdatableInterface;

class Pod implements DrawableInterface, TimeUpdatableInterface
{
    public function draw(Renderer $renderer): void
    {
    }

    public function update(float $deltaTime): void
    {
    }


}