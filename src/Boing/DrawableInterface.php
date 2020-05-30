<?php

namespace Boing;

use PhpGame\SDL\Screen;

interface DrawableInterface
{
    public function update(float $deltaTime): void;
    public function draw(Screen $screen);
}
