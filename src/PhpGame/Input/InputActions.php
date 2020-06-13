<?php

namespace PhpGame\Input;

use RuntimeException;

class InputActions
{
    private array $actions;

    /**
     * InputActions constructor.
     * @param array|InputAction[] $actions
     */
    public function __construct(array $actions)
    {
        $this->actions = $actions;
    }

    public function updateByKeyboard(Keyboard $keyboard): void
    {
        foreach ($this->actions as $bindings) {
            $bindings->updateByKeyboard($keyboard);
        }
    }

    public function getValueForAction($name)
    {
        if (!isset($this->actions[$name])) {
            throw new RuntimeException("Wrong input action name: ".$name);
        }

        return $this->actions[$name]->getValue();
    }
}

