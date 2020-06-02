<?php

namespace PhpGame\SDL;

class Screen
{
    private int $width;
    private int $height;
    private $window;
    private $renderer;
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
        $this->renderer = \SDL_CreateRenderer($this->window, 0, SDL_RENDERER_ACCELERATED);

        \SDL_SetRenderDrawColor($this->renderer, 0, 26, 33, 0);
        \SDL_RenderClear($this->renderer);
        \SDL_RenderPresent($this->renderer);
    }

    public function __destruct()
    {
        \SDL_DestroyRenderer($this->renderer);
        unset($this->window);
        \SDL_Quit();
    }

    public function drawImage(string $name, $posX, $posY, $width, $height): void
    {
        $destinationRect = new \SDL_Rect($posX, $posY, $width, $height);

        if (!isset($this->textures[$name])) {
            $this->textures[$name] = Texture::loadFromFile($name, $this->renderer);
        }

        if (\SDL_RenderCopy($this->renderer, $this->textures[$name]->getContent(), null, $destinationRect) !== 0) {
            echo \SDL_GetError(), PHP_EOL;
        }
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function clear(): void
    {
        \SDL_RenderClear($this->renderer);
    }

    public function render()
    {
        \SDL_RenderPresent($this->renderer);
    }
}
