<?php

namespace PhpGame\Input;

use PhpGame\Vector2Float;
use RuntimeException;

class InputActions
{
    private array $actions;
    /**
     * @var Keyboard
     */
    private Keyboard $keyboard;

    /**
     * InputActions constructor.
     * @param array|InputAction[] $actions
     * @param Keyboard            $keyboard
     */
    public function __construct(array $actions, Keyboard $keyboard)
    {
        $this->actions = $actions;
        $this->keyboard = $keyboard;
    }

    public function update(): void
    {
        $this->keyboard->update();
        foreach ($this->actions as $bindings) {
            $bindings->updateByKeyboard($this->keyboard);
        }
    }

    /**
     * @param $name
     * @return bool|Vector2Float
     */
    public function getValueForAction($name)
    {
        if (!isset($this->actions[$name])) {
            throw new RuntimeException("Wrong input action name: ".$name);
        }

        return $this->actions[$name]->getValue();
    }

    public function getKeyboard(): Keyboard
    {
        return $this->keyboard;
    }
}

