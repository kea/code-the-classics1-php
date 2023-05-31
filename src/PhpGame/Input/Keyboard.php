<?php

declare(strict_types=1);

namespace PhpGame\Input;

class Keyboard
{
    /** @var array<int> */
    private array $keys = [];
    /** @var array<int> */
    private array $up = [];
    /** @var array<int> */
    private array $down = [];

    public function update(): void
    {
        $numkeys = 0;
        $keys = array_filter(SDL_GetKeyboardState($numkeys), fn($key) => $key !== 0);
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
    public function getKey(int $key): bool
    {
        return isset($this->keys[$key]);
    }

    /**
     * Returns true during the frame the user starts pressing down the key
     *
     * @param int $key
     * @return bool
     */
    public function getKeyDown(int $key): bool
    {
        return isset($this->down[$key]);
    }

    /**
     * Returns true during the frame the user releases the key
     *
     * @param int $key
     * @return bool
     */
    public function getKeyUp(int $key): bool
    {
        return isset($this->up[$key]);
    }
}
