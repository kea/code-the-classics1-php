<?php

namespace Cavern;

use PhpGame\Animation;
use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use PhpGame\SDL\Renderer;
use PhpGame\SoundManager;
use PhpGame\TextureRepository;
use PhpGame\TimeUpdatableInterface;
use PhpGame\Vector2Float;

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

        $soundManager->playMusic('theme.ogg');
        $soundManager->setMusicVolume(0.3);
    }

    public function update(float $deltaTime): void
    {
        if ($this->state === self::GAME_OVER) {
            if (!$this->inputActions->getValueForAction('Fire')) {
                return;
            }
            $this->startMenu();

            return;
        }
        if ($this->state === self::MENU) {
            $this->updateMenu($deltaTime);
            if ($this->inputActions->getKeyboard()->getKeyDown(SDL_SCANCODE_SPACE)) {
                $this->startGame(true);
                $this->state = self::PLAY;
            }
        }
        if ($this->state === self::PLAY) {
            if ($this->game->isGameOver()) {
                $this->state = self::GAME_OVER;
                $this->soundManager->playSound("over0.ogg");
            }
        }

        $this->game->update($deltaTime);
    }

    public function draw(Renderer $renderer): void
    {
        $this->game->draw($renderer);
        if ($this->state === self::MENU) {
            $renderer->drawImage(__DIR__."/images/title.png", 0, 0);
            $renderer->drawImage($this->menuAnimation->getCurrentFrame(), 130, 280);
        }
        if ($this->state === self::GAME_OVER) {
            $renderer->drawImage(__DIR__."/images/over.png", 0, 0);
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
        $this->startGame(false);
    }

    private function updateMenu(float $deltaTime): void
    {
        $this->menuAnimation->update($deltaTime);
    }

    public function startGame(bool $withPlayer): void
    {
        $fruitSprite = new \Cavern\Animator\Fruit($this->textureRepository);
        $popSprite = new \Cavern\Animator\Pop($this->textureRepository);
        $orbSprite = new \Cavern\Animator\Orb($this->textureRepository);

        $fruits = new FruitCollection($fruitSprite, $this->soundManager);
        $pops = new PopCollection($popSprite);
        $orbs = new OrbCollection($orbSprite, $fruits, $pops, $this->soundManager);

        $level = new Level($this->width, $this->height, $this->textureRepository);
        $player = null;
        if ($withPlayer) {
            $playerSprite = new \Cavern\Animator\Player($this->textureRepository);
            $playerSprite->getSprite()->setPosition(new Vector2Float(200, 200));
            $player = new Player($playerSprite, $this->inputActions, $orbs);
            $player->setSoundManager($this->soundManager);
            $player->setLevel($level);
        }
        $this->game = new Game($level, $orbs, $fruits, $pops, $this->textureRepository, $player);
        $this->game->setSoundManager($this->soundManager);
        $this->game->start();
    }
}