<?php

namespace PhpGame\Input;

class ArrowKeysBinding extends DirectionBinding
{
    protected array $keys = [
        self::Up => \SDL_SCANCODE_UP,
        self::Down => \SDL_SCANCODE_DOWN,
        self::Left => \SDL_SCANCODE_LEFT,
        self::Right => \SDL_SCANCODE_RIGHT,
    ];
}