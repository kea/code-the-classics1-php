<?php

namespace Cavern;

use Cavern\Sprite\Fruit as SpriteFruit;
use PhpGame\SDL\Renderer;

class Fruit extends GravityActor
{
    public const APPLE = 0;
    public const RASPBERRY = 1;
    public const LEMON = 2;
    public const EXTRA_HEALTH = 3;
    public const EXTRA_LIFE = 4;
    private int $type;
    private float $timeToLive;
    private PopCollection $pops;

    public function __construct(
        SpriteFruit $sprite,
        PopCollection $pops,
        int $trappedEnemyType = Robot::TYPE_NORMAL
    ) {
        parent::__construct($sprite);
        $this->type = $this->randomType($trappedEnemyType);
        $this->timeToLive = 8.3;
        $this->pops = $pops;
    }

    public function isActive(): bool
    {
        return $this->timeToLive > 0;
    }

    public function update(float $deltaTime): void
    {
        parent::update($deltaTime);

        $this->timeToLive -= $deltaTime;

        if ($this->timeToLive < 0) {
            $this->pop();
        }
        $this->sprite->updateImage($this->timeToLive, $this->type);
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->render($renderer);
        $renderer->drawRectangle($this->getCollider());
    }

    public function onCollision(ColliderActor $other): void
    {
        if (!$other instanceof Player) {
            return;
        }

        if ($this->type === self::EXTRA_HEALTH) {
            $other->incHealth();
            //game.play_sound("bonus");
        } elseif ($this->type === self::EXTRA_LIFE) {
            $other->incLives();
            //game.play_sound("bonus");
        } else {
            $other->addScore(($this->type + 1) * 100);
            //game.play_sound("score");
        }
        $this->timeToLive = 0;
        $this->pop();
    }

    public function pop(): void
    {
        $this->pops->add($this->pops->createPop($this->getPosition(), Pop::TYPE_FRUIT));
    }

    private function randomType(int $trappedEnemyType): int
    {
        $maxRange = $trappedEnemyType === Robot::TYPE_NORMAL ? 29 : 39;

        $typeProbability = random_int(0, $maxRange);
        if ($typeProbability < 10) {
            return self::APPLE;
        }
        if ($typeProbability < 20) {
            return self::RASPBERRY;
        }
        if ($typeProbability < 30) {
            return self::LEMON;
        }
        if ($typeProbability < 39) {
            return self::EXTRA_HEALTH;
        }

        return self::EXTRA_LIFE;
    }
}