<?php

declare(strict_types=1);

namespace Cavern;

use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;
use PhpGame\TimeUpdatableInterface;
use PhpGame\Vector2Float;

class Player extends GravityActor implements DrawableInterface, TimeUpdatableInterface, SoundEmitterInterface
{
    use SoundEmitterTrait;

    private const MAX_HEALTH = 3;
    private const SPEED = 60 * 4;
    private int $lives = 2;
    private int $health = self::MAX_HEALTH;
    private int $score = 0;
    private float $timer = .0;
    private float $fireTimer = .0;
    private float $hurtTimer = .0;
    private InputActions $inputActions;
    private OrbCollection $orbs;
    private bool $fireDown = false;
    private ?Orb $blowingOrb = null;
    private Animator\Player $animator;

    public function __construct(
        Animator\Player $animator,
        InputActions $inputActions,
        OrbCollection $orbCollection
    ) {
        parent::__construct($animator->getSprite());
        $this->animator = $animator;
        $this->inputActions = $inputActions;
        $this->orbs = $orbCollection;
    }

    public function reset(): void
    {
        $this->setPosition(new Vector2Float(400, 100));
        $this->velocityY = 0;
        $this->directionX = 1;
        $this->fireTimer = 0;
        $this->hurtTimer = 0;
        $this->health = 3;
    }

    public function update(float $deltaTime): void
    {
        $direction = new Vector2Float(.0, .0);
        $this->timer += $deltaTime;
        $this->collisionDetection = $this->health > 0;
        parent::update($deltaTime);

        $this->fireTimer -= $deltaTime;
        $this->hurtTimer -= $deltaTime;

        if ($this->isLanded) {
            $this->hurtTimer = min($this->hurtTimer, 1.7);
        }

        if ($this->hurtTimer > 1.7) {
            $this->beenHurt($deltaTime);
        } else {
            /** @var Vector2Float $direction */
            $direction = $this->inputActions->getValueForAction('Move');
            if ($direction->x !== .0) {
                $this->directionX = $direction->x;
                if ($this->fireTimer < 0.17) {
                    $this->move($direction->x, 0, self::SPEED, $deltaTime);
                }
            }

            if ($this->fireTimer <= 0 && $this->fireButtonPressed()) {
                $x = min(730, max(70, $this->getPosition()->x + $this->directionX * 38));
                $y = $this->getPosition()->y - 30;

                $this->blowingOrb = $this->orbs->createOrb($x, $y, $this->directionX);
                if ($this->blowingOrb !== null) {
                    $this->orbs->add($this->blowingOrb);
                    $this->fireTimer = 0.33;
                    $soundName = 'blow'.random_int(0, 3).'.ogg';
                    $this->playSound($soundName);
                }
            }

            if (($direction->y < 0) && ($this->velocityY === 0.0) && ($this->isLanded)) {
                $this->velocityY = -17 * 60;
                $this->isLanded = false;
                $this->playSound('jump0.ogg');
            }
        }

        if (!$this->inputActions->getValueForAction('Fire')) {
            $this->blowingOrb = null;
        } elseif ($this->blowingOrb !== null) {
            $this->blowingOrb->blownTime += 4 / 60;
            if ($this->blowingOrb->blownTime >= 2) {
                $this->blowingOrb = null;
            }
        }

        $this->animator->setFloat('dx', $direction->x);
        $this->animator->setFloat('directionX', $this->directionX);
        $this->animator->setFloat('timer', $this->timer);
        $this->animator->setFloat('hurtTimer', $this->hurtTimer);
        $this->animator->setFloat('fireTimer', $this->fireTimer);
        $this->animator->setInt('health', $this->health);
        $this->animator->update($deltaTime);
    }

    public function draw(Renderer $renderer): void
    {
        $this->animator->getSprite()->draw($renderer);
    }

    public function onCollision(ColliderActor $other): void
    {
        if ($this->hurtTimer > 0) {
            return;
        }

        if ($other instanceof Bolt) {
            $this->hurtTimer = 200 / 60;
            --$this->health;
            $this->velocityY -= 12 * 60;
            $this->isLanded = false;
            $this->directionX = $other->directionX;
            if ($this->health > 0) {
                $soundName = 'ouch'.random_int(0, 3).'.ogg';
                $this->playSound($soundName);
            } else {
                $this->playSound('die0.ogg');
            }
        }
    }

    private function beenHurt(float $deltaTime): void
    {
        if ($this->health > 0) {
            $this->move($this->directionX, 0, self::SPEED, $deltaTime);

            return;
        }

        if ($this->sprite->top() >= self::SCREEN_HEIGHT * 1.5) {
            --$this->lives;
            $this->reset();
        }
    }

    public function incHealth(): void
    {
        $this->health = min($this->health + 1, self::MAX_HEALTH);
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    public function incLives(): void
    {
        $this->lives++;
    }

    public function getLives(): int
    {
        return $this->lives;
    }

    public function addScore(int $scoreToAdd): void
    {
        $this->score += $scoreToAdd;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    private function fireButtonPressed(): bool
    {
        if ($this->inputActions->getValueForAction('Fire')) {
            if ($this->fireDown) {
                return false;
            }
            $this->fireDown = true;

            return true;
        }

        $this->fireDown = false;

        return false;
    }
}