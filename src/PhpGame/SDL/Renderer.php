<?php

namespace PhpGame\SDL;

class Renderer
{
    /** @var resource */
    private $renderer;
    private array $clearColor = [0, 26, 33, 0];
    /** @var array|Texture[] */
    private array $textures;

    /**
     * Render constructor.
     * @param \SDL_Window $window
     */
    public function __construct(\SDL_Window $window)
    {
        $this->renderer = \SDL_CreateRenderer($window, 0, 0);

        \SDL_SetRenderDrawColor($this->renderer, ...$this->clearColor);
        \SDL_RenderClear($this->renderer);
        \SDL_RenderPresent($this->renderer);
    }

    public function __destruct()
    {
        \SDL_DestroyRenderer($this->renderer);
    }

    public function copy(Texture $texture, ?\SDL_Rect $sourceRect = null, ?\SDL_Rect $destinationRect = null)
    {
        if (\SDL_RenderCopy($this->renderer, $texture->getContent(), $sourceRect, $destinationRect) !== 0) {
            throw new \RuntimeException(\SDL_GetError());
        }
    }

    /**
     * @param string $imageFile
     * @return resource
     */
    public function loadTexture(string $imageFile)
    {
        return \IMG_LoadTexture($this->renderer, $imageFile);
    }

    public function clear(): void
    {
        \SDL_RenderClear($this->renderer);
    }

    public function render(): void
    {
        \SDL_RenderPresent($this->renderer);
    }

    public function setClearColor(array $rgba)
    {
        $this->clearColor = $rgba;
    }

    public function drawImage(string $name, int $posX, int $posY, int $width, int $height): void
    {
        $destinationRect = new \SDL_Rect($posX, $posY, $width, $height);

        if (!isset($this->textures[$name])) {
            $this->textures[$name] = Texture::loadFromFile($name, $this);
        }

        $this->copy($this->textures[$name], null, $destinationRect);
    }
}