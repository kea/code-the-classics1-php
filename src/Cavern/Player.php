<?php

declare(strict_types=1);

namespace Cavern;

use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use PhpGame\SDL\Renderer;
use PhpGame\Vector2Float;

class Player extends GravityActor implements DrawableInterface
{
    private const MAX_HEALTH = 3;
    private const SPEED = 60 * 4;
    private int $lives = 2;
    private int $health = self::MAX_HEALTH;
    private int $score = 0;
    private float $timer = .0;
    private float $fireTimer = .0;
    private float $hurtTimer = .0;
    private InputActions $inputActions;
    private string $image = 'blank';
    private OrbCollection $orbs;
    private bool $fireDown = false;
    private ?Orb $blowingOrb = null;

    public function __construct(
        Vector2Float $position,
        int $width,
        int $height,
        InputActions $inputActions,
        OrbCollection $orbCollection
    ) {
        parent::__construct($position, $width, $height);
        $this->inputActions = $inputActions;
        $this->orbs = $orbCollection;
    }

    public function reset()
    {
        $this->position = new Vector2Float(400, 100);
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
            $direction = $this->inputActions->getValueForAction('Move');
            if ($direction->x !== .0) {
                $this->directionX = $direction->x;
                if ($this->fireTimer < 0.17) {
                    $this->move($direction->x, 0, self::SPEED, $deltaTime);
                }
            }

            if ($this->fireTimer <= 0 && $this->fireButtonPressed()) {
                $x = min(730, max(70, $this->position->x + $this->directionX * 38));
                $y = $this->position->y;

                $this->blowingOrb = $this->orbs->createOrb($x, $y, $this->directionX);
                if ($this->blowingOrb !== null) {
                    $this->orbs->add($this->blowingOrb);
                    $this->fireTimer = 0.33;
                    //sound->play("blow", 4);
                }
            }

            if (($direction->y < 0) && ($this->velocityY === 0.0) && ($this->isLanded)) {
                $this->velocityY = -17 * 60;
                $this->isLanded = false;
                // sound->play("jump");
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

        $this->chooseImage($direction->x);
    }

    public function draw(Renderer $renderer): void
    {
        $name = __DIR__.'/images/'.$this->image.'.png';
        $renderer->drawImage(
            $name,
            (int)($this->position->x - $this->width / 2),
            (int)($this->position->y - $this->height)
        );
        $renderer->drawRectangle($this->getCollider());
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
                // $game->playSound("ouch", 4);
            } else {
                // $game->playSound("die");
            }
        }
    }

    private function beenHurt(float $deltaTime): void
    {
        if ($this->health > 0) {
            $this->move($this->directionX, 0, self::SPEED, $deltaTime);

            return;
        }

        if ($this->top() >= self::SCREEN_HEIGHT * 1.5) {
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

    private function chooseImage(float $dx): void
    {
        $image = "blank";
        if ($this->hurtTimer <= 0 || round($this->hurtTimer * 60) % 2 === 1) {
            $dirIndex = $this->directionX > 0 ? "1" : "0";
            if ($this->hurtTimer > 1.7) {
                if ($this->health > 0) {
                    $image = "recoil".$dirIndex;
                } else {
                    $image = "fall".((int)($this->timer * 12.5) % 2);
                }
            } elseif ($this->fireTimer > .0) {
                $image = "blow".$dirIndex;
            } elseif ($dx === 0.0) {
                $image = "still";
            } else {
                $image = "run".$dirIndex.((int)($this->timer * 7.5) % 4);
            }
        }
        $this->image = $image;
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