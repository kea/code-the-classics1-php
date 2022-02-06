<?php

declare(strict_types=1);

namespace Myriapod\Enemy;

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
        bool $totem
    ) {
        $anchor = Anchor::CenterBottom(); // (24, 60)
        $this->sprite = new Sprite($textureRepository['blank.png'], $position->x, $position->y, $anchor);

        $this->type = random_int(0, 3);

        if ($totem) {
            $this->playSound("totem_create.ogg");
            $this->health = 5;
            $this->showHealth = 5;
        } else {
            $this->health = random_int(3, 4);
            $this->showHealth = 1;
        }
    }

    public function damage(int $amount, bool $damagedByBullet = false): bool
    {
        if ($damagedByBullet && $this->health === 5) {
            $this->playSound("totem_destroy.ogg");
//            game.score += 100
        } elseif ($amount > $this->health - 1) {
            $this->playSound("rock_destroy.ogg");
        } else {
            $this->playSound("hit", 4);
        }
//        game.explosions.append(Explosion($this->pos, 2 * ($this->health == 5)))
        $this->health -= $amount;
        $this->showHealth = $this->health;

        return $this->health < 1;
    }

    public function update(float $deltaTime): void
    {
        $this->timer += $deltaTime;
        $this->timerAnimation += $deltaTime;

        if ($this->showHealth < $this->health && $this->timerAnimation > 0.333) {
            ++$this->showHealth;
            $this->timerAnimation -= 0.333;
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
}
