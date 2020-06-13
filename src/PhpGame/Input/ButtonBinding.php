<?php

namespace PhpGame\Input;

class ButtonBinding
{
    protected array $keys = [];

    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    public function updateByKeyboard(Keyboard $keyboard, bool $pressed): bool
    {
        foreach ($this->keys as $key) {
            if ($keyboard->getKey($key)) {
                $pressed = true;
            }
        }

        return $pressed;
    }
}
