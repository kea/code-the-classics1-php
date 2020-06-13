<?php

namespace PhpGame\SDL;

use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use PhpGame\Input\Keyboard;

class Engine
{
    private Renderer $renderer;
    private Keyboard $keyboard;
    private InputActions $inputActions;

    public function __construct(Renderer $screen, InputActions $inputActions, Keyboard $keyboard)
    {
        $this->renderer = $screen;
        $this->keyboard = $keyboard;
        $this->inputActions = $inputActions;
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
            //$this->>joystick->update();
            $this->inputActions->updateByKeyboard($this->keyboard);

            $this->renderer->clear();
            $game->update($deltaTime);
            $game->draw($this->renderer);

            $this->renderer->render();
            \SDL_Delay(10);
        }
    }
}

