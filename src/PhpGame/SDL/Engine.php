<?php

namespace PhpGame\SDL;

use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use SDL_Event;

use function SDL_Delay;
use function SDL_PollEvent;

class Engine
{
    private Renderer $renderer;
    private InputActions $inputActions;
    private int $framePerSecond = 60;

    public function __construct(Renderer $screen, InputActions $inputActions)
    {
        $this->renderer = $screen;
        $this->inputActions = $inputActions;
    }

    public function run(DrawableInterface $game): void
    {
        $event = new SDL_Event();
        $ticker = new Ticker();
        $frameRateTimer = new Ticker();
        while (true) {
            $deltaTime = $ticker->tick();
            $frameRateTimer->tick();

            if (SDL_PollEvent($event)) {
                switch ($event->type) {
                    case SDL_QUIT:
                        return;
                }
            }
            $this->inputActions->update();

            $this->renderer->clear();
            $game->update($deltaTime);
            $game->draw($this->renderer);
            $this->renderer->render();

            $waitTime = (1000 / $this->framePerSecond) - ($frameRateTimer->tick() * 1000);
            SDL_Delay($waitTime > 0 ? $waitTime : 0);
        }
    }

    public function setFramePerSecond(int $framePerSecond): void
    {
        $this->framePerSecond = $framePerSecond;
    }
}

