<?php

namespace Cavern;

use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use PhpGame\SDL\Renderer;
use PhpGame\Vector2Float;

class Player extends GravityActor implements DrawableInterface
{
    private const MAX_HEALTH = 3;
    private int $lives = 2;
    private int $health = self::MAX_HEALTH;
    private int $score = 0;
    private int $directionX;
    private float $fireTimer;
    private float $hurtTimer;
    private InputActions $inputActions;

    public function __construct(Vector2Float $position, int $width, int $height, InputActions $inputActions)
    {
        parent::__construct($position, $width, $height);
        $this->inputActions = $inputActions;
    }

    public function reset()
    {
        $this->position = new Vector2Float(400, 100);
        $this->velocityY = 0;
        $this->directionX = 1;
        $this->fireTimer = 0;
        $this->hurtTimer = 0;
        $this->health = 3;
        $this->blowingOrb = null;
    }

    public function update(float $deltaTime): void
    {
        parent::update($deltaTime);
        $direction = $this->inputActions->getValueForAction('Move');
        $this->move($direction->x, 0, 60 * 4, $deltaTime);

        if ($direction->y < 0 && $this->velocityY === 0.0 && $this->isLanded) {
            $this->velocityY = -16 * 60;
            $this->isLanded = false;
            // sound->play("jump");
        }
    }

    public function draw(Renderer $renderer): void
    {
        $name = __DIR__.'/images/run00.png';
        $renderer->drawImage($name, (int)($this->position->x - 70 / 2), (int)($this->position->y - 70), 70, 70);
        $renderer->drawRectangle($this->getCollider());
    }

    public function onCollision(ColliderActor $other): void
    {
        if ($other instanceof Block) {
            $this->velocityY = 0;
            $this->isLanded = true;
        }

        return;

        if (!$this->collidePoint($other->position) || $this->hurtTimer > 0) {
            return;
        }

        $this->hurtTimer = 200 / 60;
        --$this->health;
        $this->velocityY -= 12 / 60;
        $this->landed = false;
        $this->directionX = $other->directionX;
        if ($this->health > 0) {
            // $game->playSound("ouch", 4);
        } else {
            // $game->playSound("die");
        }
    }

    public function incHealth(): void
    {
        $this->health = min($this->health + 1, self::MAX_HEALTH);
    }

    public function incLives(): void
    {
        $this->lives++;
    }

    public function addScore(int $scoreToAdd): void
    {
        $this->score += $scoreToAdd;
    }
}