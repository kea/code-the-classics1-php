<?php

namespace PhpGame\SDL;

use Boing\DrawableInterface;
use PhpGame\Keyboard;

class Screen
{
    private int $width;
    private int $height;
    private $window;
    private $renderer;
    /** @var array|Texture[] */
    private array $textures = [];
    private string $title;
    private \stdClass $input;

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

    public function run(DrawableInterface $game, Keyboard $keyboard)
    {
        $event = new \SDL_Event;
        $ticker = new Ticker();
        while (true) {
            $deltaTime = $ticker->tick();

            if (\SDL_PollEvent($event)) {
                switch ($event->type) {
                    case SDL_QUIT:
                        return;
                        break;
                }
            }
            $keyboard->update();

            \SDL_RenderClear($this->renderer);

            $game->update($deltaTime);
            $game->draw($this);

            \SDL_RenderPresent($this->renderer);
            \SDL_Delay(10);
        }
    }

    public function __destruct()
    {
        \SDL_DestroyRenderer($this->renderer);
        unset($this->window);
        \SDL_Quit();
    }

    public function drawImage(string $name, $posX, $posY, $width, $height)
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
}

class Ticker
{
    private float $deltaTime;
    private float $lastTime = 0;
    private float $elapsedTime = 0;
    private int $frame = 0;

    public function __construct()
    {
        $this->lastTime = microtime(true);
    }

    public function tick(): float
    {
        $currentTime = microtime(true);
        $this->deltaTime = $currentTime - $this->lastTime;
        $this->lastTime = $currentTime;
        ++$this->frame;
        $this->elapsedTime += $this->deltaTime;
        if ($this->frame === 60) {
            $this->log();
            $this->elapsedTime = $this->frame = 0;
        }

        return $this->deltaTime;
    }

    private function log(): void
    {
        echo "\nFPS: ".round($this->frame / $this->elapsedTime);
    }
}