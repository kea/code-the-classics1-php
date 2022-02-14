<?php

declare(strict_types=1);

namespace Myriapod\Enemy;

use Myriapod\Explosion\Explosion;
use Myriapod\Explosion\Explosions;
use PhpGame\Anchor;
use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;
use PhpGame\Sprite;
use PhpGame\TextureRepository;
use PhpGame\TimeUpdatableInterface;
use PhpGame\Vector2Float;

class Rock implements DrawableInterface, TimeUpdatableInterface, SoundEmitterInterface
{
    use SoundEmitterTrait;

    private Sprite $sprite;
    private int $health;
    private int $showHealth;
    private float $timer = .0;
    private float $timerAnimation = .0;
    private int $type;

    public function __construct(
        private TextureRepository $textureRepository,
        Vector2Float $position,
        private int $wave,
        bool $totem,
        private Explosions $explosions
    ) {
        if ($totem) {
            $this->health = 5;
            $this->showHealth = 5;
            $anchor = new Anchor(0, 0.5);
        } else {
            $this->health = random_int(3, 4);
            $this->showHealth = 1;
            $anchor = Anchor::LeftTop();
        }
        $this->sprite = new Sprite($textureRepository['blank.png'], $position->x + 16, $position->y, $anchor);

        $this->type = random_int(0, 3);

    }

    public function damage(int $amount, bool $damagedByBullet = false): bool
    {
        if ($damagedByBullet && $this->health === 5) {
            $this->playSound("totem_destroy0.ogg");
//            game.score += 100
        } elseif ($amount > $this->health - 1) {
            $this->playSound("rock_destroy0.ogg");
        } else {
            $this->playSound("hit".random_int(0, 3).'.ogg');
        }
        if ($this->health === 5) {
            $this->sprite->setAnchor(Anchor::LeftTop());
        }
        $this->explosions->addExplosion($this->getPosition(), ($this->health === 5) ? Explosion::ENEMY : Explosion::ROCK);
        $this->health -= $amount;
        $this->showHealth = $this->health;

        return $this->health < 1;
    }

    public function update(float $deltaTime): void
    {
        $this->timer += $deltaTime;
        $this->timerAnimation += $deltaTime;

        if ($this->showHealth < $this->health && $this->timerAnimation > 0.033) {
            ++$this->showHealth;
            $this->timerAnimation -= 0.033;
        }
        if ($this->health === 5 && $this->timer > 200/60) {
            $this->damage(1);
        }
        $colour = max($this->wave, 0) % 3;
        $health = max($this->showHealth - 1, 0);

        $this->sprite->updateTexture($this->textureRepository["rock".$colour.$this->type.$health.".png"]);
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->draw($renderer);
    }

    public function getPosition(): Vector2Float
    {
        return $this->sprite->getPosition();
    }

    public function getCollider(): \SDL_Rect
    {
        return $this->sprite->getBoundedRect();
    }

    /** :D */
    public function isAlive(): bool
    {
        return $this->health > 0;
    }

    public function updateWave(int $wave): void
    {
        $this->wave = $wave;
    }
}
