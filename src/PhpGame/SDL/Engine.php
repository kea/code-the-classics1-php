<?php

namespace PhpGame\SDL;

use PhpGame\DrawableInterface;
use PhpGame\ECS\Registry;
use PhpGame\Input\InputActions;
use SDL_Event;

use function SDL_Delay;
use function SDL_PollEvent;

class Engine
{
    private Renderer $renderer;
    private InputActions $inputActions;
    private Registry $registry;

    public function __construct(Renderer $screen, InputActions $inputActions, Registry $registry)
    {
        $this->renderer = $screen;
        $this->inputActions = $inputActions;
        $this->registry = $registry;
    }

    public function run(DrawableInterface $game): void
    {
        $event = new SDL_Event();
        $ticker = new Ticker();
        while (true) {
            $deltaTime = $ticker->tick();

            if (SDL_PollEvent($event)) {
                switch ($event->type) {
                    case SDL_QUIT:
                        return;
                }
            }
            $this->inputActions->update();

            $this->renderer->clear();
            $jobs = $game->getJobs();
            foreach ($jobs as $job) {
                $job($this->registry);
            }
            $game->update($deltaTime);
            $game->draw($this->renderer);

            $this->renderer->render();
            SDL_Delay(10);
        }
    }
}

