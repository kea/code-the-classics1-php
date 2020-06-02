<?php

namespace PhpGame;

class Keyboard
{
    private array $keys = [];
    private array $up = [];
    private array $down = [];

    public function update()
    {
        $numkeys = 0;
        $keys = array_flip(SDL_GetKeyboardState($numkeys, false));
        $this->down = array_diff($this->keys, $keys);
        $this->up = array_diff($keys, $this->keys);
        $this->keys = $keys;
    }

    /**
     * Returns true while the user holds down the key
     *
     * @param int $key
     * @return bool
     */
    public function getKey(int $key)
    {
        return isset($this->keys[$key]);
    }

    /**
     * Returns true during the frame the user starts pressing down the key
     *
     * @param int $key
     * @return bool
     */
    public function getKeyDown(int $key)
    {
        return isset($this->down[$key]);
    }

    /**
     * Returns true during the frame the user releases the key
     *
     * @param int $key
     * @return bool
     */
    public function getKeyUp(int $key)
    {
        return isset($this->up[$key]);
    }
}