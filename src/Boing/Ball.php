<?php
declare(strict_types=1);

namespace Boing;

use PhpGame\SDL\Screen;

class Ball implements DrawableInterface
{
    private float $x = 0;
    private float $y = 0;
    private float $dx = 0;
    private float $dy = 0;
    private int $speed = 200;

    private int $fieldWidth;
    private int $fieldHalfWidth;
    private int $fieldHeight;
    private int $fieldHalfHeight;
    /**
     * @var Game
     */
    private Game $gameToBeRemoved;

    public function __construct(float $dx, int $fieldWidth, int $fieldHeight, Game $gameToBeRemoved)
    {
        $this->dx = $dx;
        $this->dy = random_int(-100, 100) / 100;
        $this->fieldWidth = $fieldWidth;
        $this->fieldHeight = $fieldHeight;
        $this->fieldHalfWidth = $fieldWidth / 2;
        $this->fieldHalfHeight = $fieldHeight / 2;
        $this->x = $this->fieldHalfWidth;
        $this->y = $this->fieldHalfHeight;
        $this->gameToBeRemoved = $gameToBeRemoved;
    }

    public function update(float $deltaTime): void
    {
        $game = $this->gameToBeRemoved;

        $originalX = $this->x;
        $this->x += $this->dx * $deltaTime * $this->speed;
        $this->y += $this->dy * $deltaTime * $this->speed;

        $this->bounceToBat($originalX);
        // The top and bottom of the arena are 220 pixels from the centre
        if (abs($this->y - $this->fieldHalfHeight) > 220) {
            $this->dy = -$this->dy;
            $this->y += $this->dy;

            $game->impacts[] = new Impact($this->x, $this->y);

            $game->playSound("bounce", 5);
            $game->playSound("bounce_synth", 1);
        }
    }

    public function bounceToBat(float $originalX)
    {
        $game = $this->gameToBeRemoved;
        if (abs($this->x - $this->fieldHalfWidth) >= 344 && abs($originalX - $this->fieldHalfWidth) < 344) {

            if ($this->x < $this->fieldHalfWidth) {
                $new_dir_x = 1;
                $bat = $game->bats[0];
            } else {
                $new_dir_x = -1;
                $bat = $game->bats[1];
            }

            $difference_y = $this->y - $bat->y;

            if ($difference_y > -64 && $difference_y < 64) {
                $this->dx = -$this->dx;
                $this->dy += $difference_y / 128;
                $this->dy = min(max($this->dy, -1), 1);
                [$this->dx, $this->dy] = $this->normalised($this->dx, $this->dy);
                $game->impacts[] = new Impact($this->x - $new_dir_x * 10, $this->y);
                $this->speed += 15;
                $game->aiOffset = random_int(-10, 10);
                $bat->time = 10;

                $game->playSound("hit", 5);  # play every time in addition to:
                if ($this->speed <= 10) {
                    $game->playSound("hit_slow", 1);
                } elseif ($this->speed <= 12) {
                    $game->playSound("hit_medium", 1);
                } elseif ($this->speed <= 16) {
                    $game->playSound("hit_fast", 1);
                } else {
                    $game->playSound("hit_veryfast", 1);
                }
            }
        }
    }

    public function out()
    {
        return $this->x < 0 || $this->x > $this->fieldWidth;
    }

    public function draw(Screen $screen)
    {
        $name = __DIR__.'/images/ball.png';
        $screen->drawImage($name, $this->x - 24/2, $this->y - 24/2, 24, 24);
    }

    private function normalised(float $x, float $y): array
    {
        $length = hypot($x, $y);

        return [$x / $length, $y / $length];
    }

    public function x(): int
    {
        return (int)round($this->x);
    }

    public function y(): int
    {
        return (int)round($this->y);
    }
}