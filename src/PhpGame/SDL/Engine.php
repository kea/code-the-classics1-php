<?php

namespace PhpGame\SDL;

use Boing\DrawableInterface;
use PhpGame\Keyboard;

class Engine
{
    private Screen $screen;
    private Keyboard $keyboard;

    public function __construct(Screen $screen, Keyboard $keyboard)
    {
        $this->screen = $screen;
        $this->keyboard = $keyboard;
    }

    public function run(DrawableInterface $game): void
    {
        $event = new \SDL_Event;
        $ticker = new Ticker();
        while (true) {
            $deltaTime = $ticker->tick();

            if (\SDL_PollEvent($event)) {
                switch ($event->type) {
                    case SDL_QUIT:
                        return;
                }
            }
            $this->keyboard->update();

            $this->screen->clear();
            $game->update($deltaTime);
            $game->draw($this->screen);

            $this->screen->render();
            \SDL_Delay(10);
        }
    }
}

