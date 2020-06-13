<?php

namespace PhpGame\Input;

use PhpGame\Vector2Float;

class DirectionBinding
{
    public const Up = 'Up';
    public const Down = 'Down';
    public const Left = 'Left';
    public const Right = 'Right';

    protected array $keys = [];

    /**
     * DirectionBinding constructor.
     * @param array $keys
     */
    public function __construct(?array $keys = null)
    {
        if ($keys) {
            $this->keys = $keys;
        }
    }

    public function updateByKeyboard(Keyboard $keyboard, Vector2Float $direction)
    {
        $dx = $direction->x();
        $dy = $direction->y();
        if ($keyboard->getKey($this->keys[self::Up])) {
            $dy = --$dy < -1 ? -1 : $dy;
        }
        if ($keyboard->getKey($this->keys[self::Down])) {
            $dy = ++$dy > +1 ? +1 : $dy;
        }
        if ($keyboard->getKey($this->keys[self::Left])) {
            $dx = --$dx < -1 ? -1 : $dx;
        }
        if ($keyboard->getKey($this->keys[self::Right])) {
            $dx = ++$dx > +1 ? +1 : $dx;
        }

        return new Vector2Float($dx, $dy);
    }
}
