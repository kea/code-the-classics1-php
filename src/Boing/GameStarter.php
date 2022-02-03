<?php

namespace Boing;

use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use PhpGame\SDL\Renderer;
use PhpGame\SoundManager;
use PhpGame\TimeUpdatableInterface;
use PhpGame\Vector2Float;

class GameStarter implements DrawableInterface, TimeUpdatableInterface
{
    private const MENU = 0;
    private const PLAY = 1;
    private const GAME_OVER = 2;
    private const PLAYER_SPEED = 360;
    private const MAX_AI_SPEED = 360;

    private int $state;
    private Game $game;
    private SoundManager $soundManager;
    private int $playersCount = 1;
    private InputActions $inputActions;
    private int $width;
    private int $height;

    public function __construct(int $width, int $height, SoundManager $soundManager, InputActions $inputActions)
    {
        $this->width = $width;
        $this->height = $height;
        $this->soundManager = $soundManager;
        $this->startMenu();
        $this->inputActions = $inputActions;

        $soundManager->playMusic("theme.ogg");
        $soundManager->setMusicVolume(0.3);
    }

    public function ai(Bat $bat): float
    {
        $xDistance = abs($this->game->ball->x() - $bat->x);
        $targetY1 = $this->height / 2;
        $targetY2 = $this->game->ball->y() + $this->game->aiOffset;
        $weight1 = min(1, $xDistance / ($this->height / 2));
        $weight2 = 1 - $weight1;
        $targetY = ($weight1 * $targetY1) + ($weight2 * $targetY2);
        $dy = $targetY - $bat->y;

        return min(self::MAX_AI_SPEED, max(-self::MAX_AI_SPEED, $dy * 60));
    }

    public function player1Controller(Bat $bat): float
    {
        /** @var Vector2Float $move */
        $move = $this->inputActions->getValueForAction('MoveP1');

        return $move->y * self::PLAYER_SPEED;
    }

    public function player2Controller(Bat $bat): float
    {
        /** @var Vector2Float $move */
        $move = $this->inputActions->getValueForAction('MoveP2');

        return $move->y * self::PLAYER_SPEED;
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
            if ($this->inputActions->getValueForAction('MenuUp')) {
                $this->playersCount = 1;
                $this->soundManager->playSound('up.ogg');
            }
            if ($this->inputActions->getValueForAction('MenuDown')) {
                $this->playersCount = 2;
                $this->soundManager->playSound('down.ogg');
            }
            if ($this->inputActions->getValueForAction('Fire')) {
                $player2Controller = $this->playersCount === 2 ? [$this, 'player2Controller'] : [$this, 'ai'];
                $this->game = new Game($this->width, $this->height, [$this, 'player1Controller'], $player2Controller);
                $this->game->setSoundManager($this->soundManager);
                $this->state = self::PLAY;
            }
        }
        if (($this->state === self::PLAY) &&
            max($this->game->bats[0]->getScore(), $this->game->bats[1]->getScore()) > 9) {
            $this->state = self::GAME_OVER;
        }

        $this->game->update($deltaTime);
    }

    public function draw(Renderer $renderer): void
    {
        $this->game->draw($renderer);
        if ($this->state === self::MENU) {
            $renderer->drawImage(__DIR__."/images/menu".($this->playersCount - 1).".png", 0, 0, 800, 480);
        }
        if ($this->state === self::GAME_OVER) {
            $renderer->drawImage(__DIR__."/images/over.png", 0, 0, 800, 480);
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