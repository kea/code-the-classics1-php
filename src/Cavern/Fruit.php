<?php

namespace Cavern;

use Cavern\Animator\Fruit as AnimatorFruit;
use PhpGame\Animator;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;

class Fruit extends GravityActor implements SoundEmitterInterface
{
    use SoundEmitterTrait;

    public const APPLE = 0;
    public const RASPBERRY = 1;
    public const LEMON = 2;
    public const EXTRA_HEALTH = 3;
    public const EXTRA_LIFE = 4;
    private int $type;
    private float $timeToLive;
    private PopCollection $pops;
    private Animator $animator;

    public function __construct(
        Animator $animator,
        PopCollection $pops,
        int $trappedEnemyType = Robot::TYPE_NORMAL
    ) {
        parent::__construct($animator->getSprite());
        $this->type = $this->randomType($trappedEnemyType);
        $this->timeToLive = 8.3;
        $this->pops = $pops;
        $this->animator = $animator;
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
        $this->animator->setFloat('timeToLive', $this->timeToLive);
        $this->animator->setInt('type', $this->type);
        $this->animator->update($deltaTime);
    }

    public function draw(Renderer $renderer): void
    {
        $this->animator->getSprite()->draw($renderer);
    }

    public function onCollision(ColliderActor $other): void
    {
        if (!$other instanceof Player) {
            return;
        }

        if ($this->type === self::EXTRA_HEALTH) {
            $other->incHealth();
            $this->playSound('bonus0.ogg');
        } elseif ($this->type === self::EXTRA_LIFE) {
            $other->incLives();
            $this->playSound('bonus0.ogg');
        } else {
            $other->addScore(($this->type + 1) * 100);
            $this->playSound('score0.ogg');
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