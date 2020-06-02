<?php

namespace Boing;

use PhpGame\Keyboard;
use PhpGame\SDL\Screen;
use PhpGame\SoundManager;

define('MAX_AI_SPEED', 200);

class GameStarter implements DrawableInterface
{
    private const MENU = 0;
    private const PLAY = 1;
    private const GAME_OVER = 2;

    private int $state;
    private Game $game;
    private SoundManager $soundManager;
    private int $playersCount = 1;
    private Keyboard $keyboard;
    private int $width;
    private int $height;

    public function __construct(int $width, int $height, SoundManager $soundManager, Keyboard $keyboard)
    {
        $this->width = $width;
        $this->height = $height;
        $this->soundManager = $soundManager;
        $this->startMenu();
        $this->keyboard = $keyboard;
    }

    public function ai(Bat $bat): float
    {
        $xDistance = abs($this->game->ball->x() - $bat->x);
        $targetY1 = $this->height / 2;
        $targetY2 = $this->game->ball->y() + $this->game->aiOffset;
        $weight1 = min(1, $xDistance / ($this->height / 2));
        $weight2 = 1 - $weight1;
        $targetY = ($weight1 * $targetY1) + ($weight2 * $targetY2);

        return min(MAX_AI_SPEED, max(-MAX_AI_SPEED, $targetY - $bat->y));
    }

    public function player1Controller(Bat $bat): float
    {
        if ($this->keyboard->getKey(\SDL_SCANCODE_UP) ||
            $this->keyboard->getKey(\SDL_SCANCODE_A)) {
            return -150;
        }
        if ($this->keyboard->getKey(\SDL_SCANCODE_DOWN) ||
            $this->keyboard->getKey(\SDL_SCANCODE_Z)) {
            return 150;
        }

        return 0;
    }

    public function player2Controller(Bat $bat): float
    {
        if ($this->keyboard->getKey(\SDL_SCANCODE_K)) {
            return -150;
        }
        if ($this->keyboard->getKey(\SDL_SCANCODE_M)) {
            return 150;
        }

        return 0;
    }

    public function update(float $deltaTime): void
    {
        if ($this->state === self::GAME_OVER) {
            if (!$this->keyboard->getKeyDown(\SDL_SCANCODE_SPACE)) {
                return;
            }
            $this->startMenu();
            return;
        }
        if ($this->state === self::MENU) {
            if ($this->keyboard->getKeyDown(\SDL_SCANCODE_UP)) {
                $this->playersCount = 1;
                $this->soundManager->play('up');
            }
            if ($this->keyboard->getKeyDown(\SDL_SCANCODE_DOWN)) {
                $this->playersCount = 2;
                $this->soundManager->play('down');
            }
            if ($this->keyboard->getKeyDown(\SDL_SCANCODE_SPACE)) {
                $player2Controller = $this->playersCount === 2 ? [$this, 'player2Controller'] : [$this, 'ai'];
                $this->game = new Game($this->width, $this->height, [$this, 'player1Controller'], $player2Controller);
                $this->game->setSoundManager($this->soundManager);
                $this->state = self::PLAY;
            }
        }
        if ($this->state === self::PLAY) {
            if (max($this->game->bats[0]->score(), $this->game->bats[1]->score()) > 2) {
                $this->state = self::GAME_OVER;
            }
        }

        $this->game->update($deltaTime);
    }

    public function draw(Screen $screen)
    {
        $this->game->draw($screen);
        if ($this->state === self::MENU) {
            $screen->drawImage(__DIR__."/images/menu".($this->playersCount - 1).".png", 0, 0, 800, 480);
        }
        if ($this->state === self::GAME_OVER) {
            $screen->drawImage(__DIR__."/images/over.png", 0, 0, 800, 480);
        }
    }

    private function startMenu(): void
    {
        $this->state = self::MENU;
        $this->game = new Game($this->width, $this->height, [$this, 'ai'], [$this, 'ai']);
        $this->game->setSoundManager($this->soundManager);
        $this->playersCount = 1;
    }
}