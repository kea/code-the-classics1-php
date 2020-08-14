<?php

namespace Cavern;

use PhpGame\SDL\Renderer;
use PhpGame\Vector2Float;

class Robot extends GravityActor
{
    public const TYPE_NORMAL = 0;
    public const TYPE_AGGRESSIVE = 1;
    public const TYPE_NONE = -1;

    private int $type;
    private int $speed;
    private int $directionX = 1;
    private bool $alive = true;
    private float $changeDirTimer = .0;
    private float $fireTimer = 100.0;
    private float $lifeTimer = .0;
    private OrbCollection $orbs;
    private BoltCollection $bolts;
    private ?Player $player;

    public function __construct(
        Vector2Float $position,
        int $width,
        int $height,
        int $type,
        OrbCollection $orbs,
        BoltCollection $bolts,
        ?Player $player
    ) {
        parent::__construct($position, $width, $height);
        $this->type = $type;
        $this->speed = random_int(1, 3);
        $this->orbs = $orbs;
        $this->player = $player;
        $this->bolts = $bolts;
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

        if ($this->changeDirTimer <= 0) {
            $directions = [-1, 1];
            if ($this->player) {
                $directions[] = $this->sign($this->player->position->x - $this->position->x);
            }
            $this->directionX = $directions[random_int(0, count($directions) - 1)];
            $this->changeDirTimer = random_int(100, 250);
        }

        if ($this->type === self::TYPE_AGGRESSIVE && $this->fireTimer >= 24 * 60) {
            foreach ($this->orbs as $orb) {
                if ($orb->position->y >= $this->top() &&
                    $orb->position->y < $this->bottom() &&
                    abs($orb->position->x - $this->position->x) < 200
                ) {
                    $this->directionX = $this->sign($orb->position->x - $this->position->x);
                    $this->fireTimer = 0;
                    break;
                }
            }
        }

        if ($this->fireTimer >= 12 * 60) {
            $fireProbability = $this->fireProbability();
            if ($this->player && $this->top() < $this->player->bottom() && $this->bottom() > $this->player->top()) {
                $fireProbability *= 10;
            }
            if (mt_rand() < $fireProbability) {
                $this->fireTimer = 0;
                //$this->play_sound("laser", 4);
            }
        } elseif ($this->fireTimer === 8) {
            $this->bolts->add(
                new Bolt(
                    new Vector2Float($this->position->x + $this->directionX * 20, $this->position->y - 38),
                    48,
                    30,
                    $this->directionX
                )
            );
        }
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
        $directionIdx = $this->directionX > 0 ? "1" : "0";
        $image = "robot".$this->type.$directionIdx;
        if ($this->fireTimer < 12 *  60) {
            $image .= 5 + floor($this->fireTimer / (4 * 60));
        } else {
            $image .= 1 + (floor($this->lifeTimer / (4 *  60)) % 4);
        }
        $name = __DIR__.'/images/'.$image.'.png';

        $renderer->drawImage(
            $name,
            (int)($this->position->x - $this->width / 2),
            (int)($this->position->y - $this->height),
            $this->width,
            $this->height
        );
//        $renderer->drawRectangle($this->getCollider());
    }

    public function isActive(): bool
    {
        return $this->alive;
    }

    public function getType(): int
    {
        return $this->type;
    }
}