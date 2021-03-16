<?php

namespace Bunner;

interface RectangleBounded
{
    public function getBoundedRectangle(): \SDL_Rect;
}
