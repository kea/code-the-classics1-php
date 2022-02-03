<?php

namespace PhpGame;

use PhpGame\SDL\Renderer;

interface DrawableInterface
{
    public function draw(Renderer $renderer): void;
}
