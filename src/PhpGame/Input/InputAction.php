<?php

namespace PhpGame\Input;

interface InputAction
{
    public function getValue();
    public function updateByKeyboard(Keyboard $keyboard): void;
}
