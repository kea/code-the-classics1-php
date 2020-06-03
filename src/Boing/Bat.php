<?php
declare(strict_types=1);

namespace Boing;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Screen;

class Bat implements DrawableInterface
{
    private int $score = 0;
    /** @var callable */
    private $moveFunction;
    private int $playerNumber;
    public float $time = .0;
    public float $y;
    public float $x;
    private string $image = 'bat00';
    private Game $game;

    public function __construct(int $playerNumber, callable $control, Game $game)
    {
        $this->moveFunction = $control;
        $this->playerNumber = $playerNumber;
        $this->x = $playerNumber === 0 ? 40 : 760;
        $this->y = 240;

        $this->game = $game;
    }

    public function update(float $deltaTime): void
    {
        if ($this->time > 0) {
            $this->time -= $deltaTime;
        }
        $yMovement = ($this->moveFunction)($this) * $deltaTime;
        $this->y = min(400, max(80, $this->y + $yMovement));

        $frame = 0;

        $game = $this->game;
        if ($game->ball->out()) {
            $frame = $game->getScoringPlayer() === $this->playerNumber ? 1 : 2;
        }

        $this->image = "bat".$this->playerNumber.$frame;
    }

    public function draw(Screen $screen): void
    {
        $name = __DIR__.'/images/'.$this->image.'.png';
        $screen->drawImage($name, (int)($this->x - 160/2), (int)($this->y - 160/2), 160, 160);
    }

    public function incScore(): void
    {
        $this->score++;
    }

    public function score(): int
    {
        return $this->score;
    }
}
