<?php

namespace PhpGame;

use PhpGame\SDL\Renderer;

interface DrawableInterface
{
    public function update(float $deltaTime): void;
    public function draw(Renderer $renderer): void;
}
