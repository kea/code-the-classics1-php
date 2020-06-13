<?php

namespace PhpGame\Input;

class ButtonAction implements InputAction
{
    private bool $value = false;
    /** @var array|ButtonBinding[] */
    private array $bindings;

    /**
     * InputKeyAction constructor.
     * @param array|ButtonBinding[] $bindings
     */
    public function __construct(array $bindings)
    {
        $this->bindings = $bindings;
    }

    public function getValue(): bool
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
