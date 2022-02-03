<?php
declare(strict_types=1);

namespace Boing;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\TimeUpdatableInterface;

class Bat implements DrawableInterface, TimeUpdatableInterface
{
    public const PLAY = 0;
    public const HIT = 1;
    public const LOOSE = 2;
    private int $score = 0;
    /** @var callable */
    private $moveFunction;
    private int $playerNumber;
    public float $timer = -1;
    public float $y;
    public float $x;
    private int $status;

    public function __construct(int $playerNumber, callable $control)
    {
        $this->moveFunction = $control;
        $this->playerNumber = $playerNumber;
        $this->x = $playerNumber === 0 ? 40 : 760;
        $this->y = 240;
        $this->status = self::PLAY;
    }

    public function update(float $deltaTime): void
    {
        if ($this->status === self::HIT) {
            $this->timer -= $deltaTime;
            if ($this->timer < 0) {
                $this->status = self::PLAY;
            }
        }

        $yMovement = ($this->moveFunction)($this) * $deltaTime;
        $this->y = min(400, max(80, $this->y + $yMovement));
    }

    public function draw(Renderer $renderer): void
    {
        $frame = 0;
        if ($this->status === self::LOOSE) {
            $frame = 2;
        }
        if ($this->status === self::HIT) {
            $frame = 1;
        }

        $name = __DIR__.'/images/bat'.$this->playerNumber.$frame.'.png';
        $renderer->drawImage($name, (int)($this->x - 160/2), (int)($this->y - 160/2), 160, 160);
    }

    public function scored(): void
    {
        $this->score++;
    }

    public function loose(): void
    {
        $this->status = self::LOOSE;
    }

    public function play(): void
    {
        $this->status = self::PLAY;
    }

    public function hit(): void
    {
        $this->status = self::HIT;
        $this->timer = 0.167;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getStatus(): int
    {
        return $this->status;
    }
}
