<?php

namespace PhpGame\Input;

interface InputAction
{
    public function getValue(): mixed;

    public function updateByKeyboard(Keyboard $keyboard): void;
}
