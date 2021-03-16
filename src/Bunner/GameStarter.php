<?php

namespace Bunner;

use PhpGame\Animation;
use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use PhpGame\SDL\Renderer;
use PhpGame\SoundManager;
use PhpGame\TextureRepository;

use const SDL_SCANCODE_SPACE;

class GameStarter implements DrawableInterface
{
    private const MENU = 0;
    private const PLAY = 1;
    private const GAME_OVER = 2;

    private int $state;
    private Game $game;
    private SoundManager $soundManager;
    private InputActions $inputActions;
    private int $width;
    private int $height;
    private ?Animation $menuAnimation = null;
    private TextureRepository $textureRepository;

    public function __construct(
        int $width,
        int $height,
        SoundManager $soundManager,
        InputActions $inputActions,
        TextureRepository $textureRepository
    ) {
        $this->width = $width;
        $this->height = $height;
        $this->soundManager = $soundManager;
        $this->inputActions = $inputActions;
        $this->textureRepository = $textureRepository;
        $this->startMenu();
    }

    public function update(float $deltaTime): void
    {
        if ($this->state === self::GAME_OVER) {
            if (!$this->inputActions->getValueForAction('Confirm')) {
                return;
            }
            $this->startMenu();

            return;
        }
        if ($this->state === self::MENU) {
            if ($this->inputActions->getKeyboard()->getKeyDown(SDL_SCANCODE_SPACE)) {
                $this->startGame(true);
                $this->state = self::PLAY;
            } else {
                $this->updateMenu($deltaTime);
            }
        }
        if ($this->state === self::PLAY) {
            if ($this->game->isGameOver()) {
                $this->state = self::GAME_OVER;
                // save high score
            }
        }

        $this->game->update($deltaTime);
    }

    public function draw(Renderer $renderer): void
    {
        $this->game->draw($renderer);
        if ($this->state === self::MENU) {
            $renderer->drawImage(__DIR__."/images/title.png", 0, 0);
            $renderer->drawImage($this->menuAnimation->getCurrentFrame(), ($this->width - 270) / 2, $this->height - 240);
        }
        if ($this->state === self::GAME_OVER) {
            $renderer->drawImage(__DIR__."/images/gameover.png", 0, 0);
        }
    }

    private function startMenu(): void
    {
        $this->state = self::MENU;
        if ($this->menuAnimation === null) {
            $images = [
                __DIR__.'/images/start0.png',
                __DIR__.'/images/start1.png',
                __DIR__.'/images/start2.png',
                __DIR__.'/images/start1.png',
            ];

            $this->menuAnimation = new Animation($images, 10, true);
        }

        $this->menuAnimation->startAnimation();
        $this->startGame(false);
    }

    private function updateMenu(float $deltaTime): void
    {
        $this->menuAnimation->update($deltaTime);
    }

    public function startGame(bool $withPlayer): void
    {
        $this->soundManager->setMusicVolume($withPlayer ? 0.4 : 1);
        $this->soundManager->playMusic('theme.ogg');

        unset($this->game);
        $this->game = new Game($this->textureRepository, $this->soundManager, $this->inputActions);
        if ($withPlayer) {
            $this->game->addPlayer();
        }
        $this->game->start();
    }
}
