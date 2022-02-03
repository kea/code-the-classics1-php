<?php

namespace Cavern;

use Cavern\Animator\Robot as SpriteRobot;
use PhpGame\Animator;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;
use PhpGame\Vector2Float;

class Robot extends GravityActor implements SoundEmitterInterface
{
    use SoundEmitterTrait;

    public const TYPE_NORMAL = 0;
    public const TYPE_AGGRESSIVE = 1;
    public const TYPE_NONE = -1;

    private int $type;
    private int $speed;
    private bool $alive = true;
    private float $changeDirTimer = .0;
    private float $fireTimer = .0;
    private float $lifeTimer = .0;
    private OrbCollection $orbs;
    private BoltCollection $bolts;
    private ?Player $player;
    private Animator $animator;

    public function __construct(
        SpriteRobot $animator,
        int $type,
        OrbCollection $orbs,
        BoltCollection $bolts,
        Level $level,
        ?Player $player
    ) {
        parent::__construct($animator->getSprite());
        $this->type = $type;
        $this->speed = random_int(1 * 60, 3 * 60);
        $this->orbs = $orbs;
        $this->player = $player;
        $this->bolts = $bolts;
        $this->animator = $animator;
        $this->setLevel($level);
        $this->update(0);
    }

    public function update(float $deltaTime): void
    {
        parent::update($deltaTime);

        $this->changeDirTimer -= $deltaTime;
        $this->fireTimer += $deltaTime;
        $this->lifeTimer += $deltaTime;

        if ($this->move($this->directionX, 0, $this->speed, $deltaTime)) {
            $this->changeDirTimer = 0;
        }

        $this->changeDirection();
        $this->shotAtOrb();

        if ($this->fireTimer >= 12 / 60) {
            $fireProbability = $this->fireProbability();
            if ($this->isAtTheSameRowAsThePlayer()) {
                $fireProbability *= 10;
            }
            if ((random_int(0, 1000) / 1000) < $fireProbability) {
                $this->fireTimer = 0;
                $this->playSound('laser'.random_int(0, 3).'.ogg');
            }
        } elseif (($this->fireTimer >= 8 / 60) && ($this->fireTimer <= 9 / 60)) { // @todo wait shooting animation to finish
            $this->fireTimer = 9 / 60; // @todo remove when the shooting is triggered by animation
            $position = new Vector2Float(
                $this->getPosition()->x + $this->directionX * 20, $this->getPosition()->y - 38
            );
            $this->bolts->add($this->bolts->create($position, $this->directionX));
        }

        $this->animator->setFloat('directionX', $this->directionX);
        $this->animator->setFloat('lifeTimer', $this->lifeTimer);
        $this->animator->setFloat('fireTimer', $this->fireTimer);
        $this->animator->setInt('type', $this->type);
        $this->animator->update($deltaTime);
    }

    public function onCollision(ColliderActor $other): void
    {
        if ($other instanceof Orb && !$other->hasTrappedEnemy()) {
            $this->alive = false;
        }
    }

    public function fireProbability(): float
    {
        return 0.001 + (0.0001 * min(100, $this->level->level));
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->draw($renderer);
    }

    public function isActive(): bool
    {
        return $this->alive;
    }

    public function getType(): int
    {
        return $this->type;
    }

    private function shotAtOrb(): void
    {
        if ($this->type !== self::TYPE_AGGRESSIVE || $this->fireTimer < 24 * 60) {
            return;
        }

        foreach ($this->orbs as $orb) {
            if ($orb->position->y >= $this->sprite->top() &&
                $orb->position->y < $this->sprite->bottom() &&
                abs($orb->position->x - $this->getPosition()->x) < 200
            ) {
                $this->directionX = $this->sign($orb->getPosition()->x - $this->getPosition()->x);
                $this->fireTimer = 0;
                break;
            }
        }
    }

    private function changeDirection(): void
    {
        if ($this->changeDirTimer > 0) {
            return;
        }

        $directions = [-1, 1];
        if ($this->player) {
            $directions[] = $this->sign($this->player->getPosition()->x - $this->getPosition()->x);
        }
        $this->directionX = $directions[random_int(0, count($directions) - 1)];
        $this->changeDirTimer = random_int(100, 250);
    }

    private function isAtTheSameRowAsThePlayer(): bool
    {
        return $this->player
            && ($this->sprite->top() < $this->player->sprite->bottom())
            && ($this->sprite->bottom() > $this->player->sprite->top());
    }
}
