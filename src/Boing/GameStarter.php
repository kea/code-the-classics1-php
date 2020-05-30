<?php

namespace Boing;

use PhpGame\SDL\Screen;

class GameStarter implements DrawableInterface
{
    private const MENU = 0;
    private const PLAY = 1;

    private int $state;
    private Screen $screen;
    private Game $game;
    private int $playersCount = 1;

    public function __construct(Screen $screen)
    {
        $this->state = self::MENU;
        $this->game = new \Boing\Game($screen, [$this, 'ai'], [$this, 'ai']);
        $this->screen = $screen;
    }

    public function ai(Bat $bat): float
    {
        $xDistance = abs($this->game->ball->x() - $bat->x);
        $targetY1 = $this->screen->getHeight() / 2;
        $targetY2 = $this->game->ball->y() + $this->game->aiOffset;
        $weight1 = min(1, $xDistance / ($this->screen->getHeight() / 2));
        $weight2 = 1 - $weight1;
        $targetY = ($weight1 * $targetY1) + ($weight2 * $targetY2);

        return min(MAX_AI_SPEED, max(-MAX_AI_SPEED, $targetY - $bat->y));
    }

    public function update(float $deltaTime): void
    {
        $this->game->update($deltaTime);
        if ($this->state === self::MENU) {
            $numKeys = 0;
            $keyState = array_flip(\SDL_GetKeyboardState($numKeys, false));

            if (isset($keyState[\SDL_SCANCODE_UP])) {
                $this->playersCount = 1;
            }
            if (isset($keyState[\SDL_SCANCODE_DOWN])) {
                $this->playersCount = 2;
            }
            if (isset($keyState[\SDL_SCANCODE_SPACE])) {
                $this->game = new \Boing\Game($this->screen, [$this, 'ai'], [$this, 'ai']);
                $this->state = self::PLAY;
            }
        }
    }

    public function draw(Screen $screen)
    {
        $this->game->draw($this->screen);
        if ($this->state === self::MENU) {
            $this->screen->drawImage(__DIR__."/images/menu".($this->playersCount - 1).".png", 0, 0, 800, 480);
        }
    }
}