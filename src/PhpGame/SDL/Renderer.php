<?php

namespace PhpGame\SDL;

use RuntimeException;
use SDL_Rect;
use SDL_Window;

use function SDL_CreateRenderer;
use function SDL_DestroyRenderer;
use function SDL_GetError;
use function SDL_QueryTexture;
use function SDL_RenderClear;
use function SDL_RenderCopy;
use function SDL_RenderDrawRect;
use function SDL_RenderPresent;
use function SDL_SetRenderDrawColor;

class Renderer
{
    /** @var resource */
    private $renderer;
    private array $clearColor = [0, 26, 33, 0];
    /** @var array|Texture[] */
    private array $textures;

    /**
     * Render constructor.
     * @param SDL_Window $window
     */
    public function __construct(SDL_Window $window)
    {
        $this->renderer = SDL_CreateRenderer($window, 0, 0);

        SDL_SetRenderDrawColor($this->renderer, ...$this->clearColor);
        SDL_RenderClear($this->renderer);
        SDL_RenderPresent($this->renderer);
    }

    public function __destruct()
    {
        SDL_DestroyRenderer($this->renderer);
    }

    public function copy(Texture $texture, ?SDL_Rect $sourceRect = null, ?SDL_Rect $destinationRect = null): void
    {
        if (SDL_RenderCopy($this->renderer, $texture->getContent(), $sourceRect, $destinationRect) !== 0) {
            throw new RuntimeException(SDL_GetError());
        }
    }

    public function clear(): void
    {
        SDL_SetRenderDrawColor($this->renderer, ...$this->clearColor);
        SDL_RenderClear($this->renderer);
    }

    public function render(): void
    {
        SDL_RenderPresent($this->renderer);
    }

    public function setClearColor(array $rgba): void
    {
        $this->clearColor = $rgba;
    }

    public function setDrawColor(array $rgba): void
    {
        SDL_SetRenderDrawColor($this->renderer, ...$rgba);
    }

    public function drawImage(string $name, int $posX, int $posY, ?int $width = null, ?int $height = null): void
    {
        if (!isset($this->textures[$name])) {
            $this->textures[$name] = Texture::loadFromFile($name, $this);
        }

        $destinationRect = new SDL_Rect(
            $posX,
            $posY,
            $width ?? $this->textures[$name]->getWidth(),
            $height ?? $this->textures[$name]->getHeight()
        );

        $this->copy($this->textures[$name], null, $destinationRect);
    }

    public function drawRectangle(SDL_Rect $rect): void
    {
        SDL_RenderDrawRect($this->renderer, $rect);
    }

    /** @return resource */
    public function getSDLRenderer()
    {
        return $this->renderer;
    }
}
