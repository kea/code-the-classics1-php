<?php

namespace PhpGame\SDL;

class Screen
{
    private int $width;
    private int $height;
    private \SDL_Window $window;
    /** @var array|Texture[] */
    private array $textures = [];
    private string $title;

    public function __construct(int $width, int $height, string $title = 'PhpGame')
    {
        $this->width = $width;
        $this->height = $height;
        $this->title = $title;

        \SDL_Init(SDL_INIT_VIDEO);
        $windowsAttribute = \SDL_Window::SHOWN | \SDL_Window::RESIZABLE;
        $this->window = new \SDL_Window(
            $title,
            \SDL_Window::POS_UNDEFINED,
            \SDL_Window::POS_UNDEFINED,
            $this->width,
            $this->height,
            $windowsAttribute
        );
    }

    public function __destruct()
    {
        unset($this->window);
        \SDL_Quit();
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setIcon(string $file): void
    {
        $icon = SDL_LoadBMP($file);
        \SDL_SetWindowIcon($this->window, $icon);
        \SDL_FreeSurface($icon);
    }

    /**
     * @return \SDL_Window
     */
    public function getWindow(): \SDL_Window
    {
        return $this->window;
    }
}
