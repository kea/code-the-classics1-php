<?php

namespace Cavern;

use PhpGame\SDL\Renderer;
use PhpGame\Vector2Float;
use PhpGame\Vector2Int;

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
        Vector2Float $position,
        int $width,
        int $height,
        PopCollection $pops,
        int $trappedEnemyType = Robot::TYPE_NORMAL
    ) {
        parent::__construct($position, $width, $height);
        if ($trappedEnemyType === Robot::TYPE_NORMAL) {
            $this->type = array_rand([self::APPLE, self::RASPBERRY, self::LEMON]);
        } else {
            $types = array_fill(0, 10, self::APPLE);
            $types += array_fill(10, 10, self::RASPBERRY);
            $types += array_fill(20, 10, self::LEMON);
            $types += array_fill(30, 9, self::EXTRA_HEALTH);
            $types[] = self::EXTRA_LIFE;

            $this->type = array_rand($types);
        }

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
    }

    public function getCollider(): \SDL_Rect
    {
        return new \SDL_Rect(
            $this->position->x - $this->width / 2,
            $this->position->y - $this->height,
            $this->width - 5,
            $this->height - 5
        );
    }

    public function draw(Renderer $renderer): void
    {
        $animFrame = 0; //str([0, 1, 2, 1][(game.timer // 6) % 4])
        $name = __DIR__.'/images/fruit'.$this->type.$animFrame.'.png';

        $renderer->drawImage(
            $name,
            (int)($this->position->x - $this->width / 2),
            (int)($this->position->y - $this->height),
            $this->width,
            $this->height
        );
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
        echo "Add pop";
        $this->pops->add(new Pop($this->position, new Vector2Int($this->width, $this->height), Pop::TYPE_FRUIT));
    }
}