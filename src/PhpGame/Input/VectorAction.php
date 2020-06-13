<?php

namespace PhpGame\Input;

use PhpGame\Vector2Float;

class VectorAction implements InputAction
{
    private Vector2Float $value;
    /** @var array|DirectionBinding[] */
    private array $bindings;

    /**
     * InputKeyAction constructor.
     * @param array|DirectionBinding[] $bindings
     */
    public function __construct(array $bindings)
    {
        $this->bindings = $bindings;
        $this->value = new Vector2Float(0, 0);
    }

    public function getValue(): Vector2Float
    {
        return $this->value;
    }

    public function updateByKeyboard(Keyboard $keyboard): void
    {
        foreach ($this->bindings as $binding) {
            $this->value = $binding->updateByKeyboard($keyboard, $this->value);
        }
    }
}
