<?php

namespace Cavern;

use Cavern\Sprite\Robot as SpriteRobot;
use PhpGame\SDL\Renderer;
use PhpGame\Vector2Float;

class Robot extends GravityActor
{
    public const TYPE_NORMAL = 0;
    public const TYPE_AGGRESSIVE = 1;
    public const TYPE_NONE = -1;

    private int $type;
    private int $speed;
    private bool $alive = true;
    private float $changeDirTimer = .0;
    private float $fireTimer = 100.0;
    private float $lifeTimer = .0;
    private OrbCollection $orbs;
    private BoltCollection $bolts;
    private ?Player $player;

    public function __construct(
        SpriteRobot $sprite,
        int $type,
        OrbCollection $orbs,
        BoltCollection $bolts,
        Level $level,
        ?Player $player
    ) {
        parent::__construct($sprite);
        $this->type = $type;
        $this->speed = random_int(1 * 60, 3 * 60);
        $this->orbs = $orbs;
        $this->player = $player;
        $this->bolts = $bolts;
        $this->setLevel($level);
        $this->update(0);
        $this->sprite = $sprite;
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
            if ($this->player
                && ($this->sprite->top() < $this->player->sprite->bottom())
                && ($this->sprite->bottom() > $this->player->sprite->top())) {
                $fireProbability *= 10;
            }
            if ((random_int(0, 1000) / 1000) < $fireProbability) {
                $this->fireTimer = 0;
                //$this->play_sound("laser", 4);
            }
        } elseif (($this->fireTimer >= 8 / 60) && ($this->fireTimer <= 9 / 60)) { // @todo wait shooting animation to finish
            $this->fireTimer = 9 / 60; // @todo remove when the shooting is triggered by animation
            $position = new Vector2Float(
                $this->getPosition()->x + $this->directionX * 20, $this->getPosition()->y - 38
            );
            $this->bolts->add($this->bolts->create($position, $this->directionX));
        }

        $this->sprite->updateImage($this->directionX, $this->type, $this->fireTimer, $this->lifeTimer);
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
        $this->sprite->render($renderer);
        $renderer->drawRectangle($this->getCollider());
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
}