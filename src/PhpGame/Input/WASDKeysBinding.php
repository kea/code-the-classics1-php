<?php

namespace PhpGame\Input;

class WASDKeysBinding extends DirectionBinding
{
    protected array $keys = [
        self::Up => \SDL_SCANCODE_W,
        self::Down => \SDL_SCANCODE_S,
        self::Left => \SDL_SCANCODE_A,
        self::Right => \SDL_SCANCODE_D,
    ];
}
