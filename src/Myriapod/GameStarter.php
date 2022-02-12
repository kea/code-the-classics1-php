<?php

namespace Myriapod;

use PhpGame\Camera;
use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use PhpGame\SDL\Renderer;
use PhpGame\SoundManager;
use PhpGame\TextureRepository;
use PhpGame\TimeUpdatableInterface;

use const SDL_SCANCODE_SPACE;

class GameStarter implements DrawableInterface, TimeUpdatableInterface
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
    private TextureRepository $textureRepository;
    private GUI\GUI $gui;
    private Score $score;

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
        $this->init();
    }

    public function init(): void
    {
        $this->score = new Score();
        $this->score->reset();
        $guiCamera = new Camera(new \SDL_Rect(0, 0, Game::WIDTH, Game::HEIGHT));
        $this->gui = new GUI\GUI($this->textureRepository, $guiCamera, $this->score);
        $this->startMenu();
    }

    public function update(float $deltaTime): void
    {
        if ($this->state === self::GAME_OVER) {
            if (!$this->inputActions->getValueForAction('Confirm')) {
                return;
            }
            $this->startMenu();
            $this->gui->changeStateToMenu();

            return;
        }
        if ($this->state === self::MENU) {
            if ($this->inputActions->getKeyboard()->getKeyDown(SDL_SCANCODE_SPACE)) {
                $this->gui->changeStateToPlay();
                $this->startGame(true);
                $this->state = self::PLAY;
            }
        }
        if ($this->state === self::PLAY) {
            if ($this->game->isGameOver()) {
                $this->state = self::GAME_OVER;
                $this->gui->changeStateToGameOver();
            }
        }

        $this->game->update($deltaTime);
    }

    public function draw(Renderer $renderer): void
    {
        $this->game->draw($renderer);
    }

    private function startMenu(): void
    {
        $this->state = self::MENU;
        $this->gui->init();
        $this->startGame(false);
    }

    public function startGame(bool $withPlayer): void
    {
        //$this->soundManager->setMusicVolume($withPlayer ? 0.4 : 1);
        $this->soundManager->setChannelVolume(0.8);
        $this->soundManager->setMusicVolume(0.4);
        $this->soundManager->playMusic('theme.ogg');

        unset($this->game);
        $entityRegistry = new EntityRegistry();

        $this->game = new Game(
            $this->textureRepository,
            $this->soundManager,
            $this->inputActions,
            $this->gui,
            $entityRegistry,
            $this->score
        );
        if ($withPlayer) {
            $this->game->addPlayer();
            $this->gui->addPlayer($this->game->getPlayer() ?? throw new \RuntimeException("No player?!?!?"));
        }
        $this->game->start();
    }
}
