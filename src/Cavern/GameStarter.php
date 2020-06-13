<?php

namespace Cavern;

use PhpGame\Animation;
use PhpGame\DrawableInterface;
use PhpGame\Input\Keyboard;
use PhpGame\SoundManager;

class GameStarter implements DrawableInterface
{
    private const MENU = 0;
    private const PLAY = 1;
    private const GAME_OVER = 2;

    private int $state;
    private Game $game;
    private SoundManager $soundManager;
    private Keyboard $keyboard;
    private int $width;
    private int $height;
    private ?Animation $menuAnimation = null;

    public function __construct(int $width, int $height, SoundManager $soundManager, Keyboard $keyboard)
    {
        $this->width = $width;
        $this->height = $height;
        $this->soundManager = $soundManager;
        $this->startMenu();
        $this->keyboard = $keyboard;
        $this->game = new Game($this->width, $this->height);
        $this->game->setSoundManager($this->soundManager);
        $this->game->start();
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
            $this->updateMenu($deltaTime);
            if ($this->keyboard->getKeyDown(\SDL_SCANCODE_SPACE)) {
                $this->game = new Game($this->width, $this->height, new Player(new \SDL_Point(200, 200), 70, 70));
                $this->game->setSoundManager($this->soundManager);
                $this->game->start();
                $this->state = self::PLAY;
            }
        }
        if ($this->state === self::PLAY) {
            //if ($this->game->player->life() < 0) {
            //    $this->state = self::GAME_OVER;
            //}
        }

        $this->game->update($deltaTime);
    }

    public function draw(\PhpGame\SDL\Renderer $renderer): void
    {
        if ($this->state === self::PLAY) {
            $this->game->draw($renderer);
        }
        if ($this->state === self::MENU) {
            $renderer->drawImage(__DIR__."/images/title.png", 0, 0, 800, 480);
            $renderer->drawImage($this->menuAnimation->getCurrentFrame(), 130, 280, 540, 90);
        }
        if ($this->state === self::GAME_OVER) {
            $renderer->drawImage(__DIR__."/images/over.png", 0, 0, 800, 480);
        }
    }

    private function startMenu(): void
    {
        $this->state = self::MENU;
        if ($this->menuAnimation === null) {
            $images = [];
            for ($i = 0; $i < 10; ++$i) {
                $images[] = __DIR__.'/images/space'.$i.'.png';
            }
            $this->menuAnimation = new Animation($images, 12, true);
        }

        $this->menuAnimation->startAnimation();
    }

    private function updateMenu(float $deltaTime)
    {
        $this->menuAnimation->update($deltaTime);
    }
}