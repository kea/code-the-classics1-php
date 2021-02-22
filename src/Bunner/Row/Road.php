<?php

namespace Bunner\Row;

use Bunner\Obstacle\Car;
use Bunner\Player\PlayerState;
use PhpGame\TextureRepository;

class Road extends ActiveRow
{
    protected string $textureName = 'road%d.png';
    protected string $childType = Car::class;

    public function __construct(TextureRepository $textureRepository, int $index, ?Row $previous = null)
    {
        $dxs = array_merge(range(-5, -1), range(1, 5));
        if ($previous instanceof ActiveRow) {
            $dxs = array_diff($dxs, [$previous->dx]);
        }
        $this->dx = $dxs[array_rand($dxs)];
        parent::__construct($textureRepository, $index, $previous);
    }

    public function nextRow(): Row
    {
        if ($this->index === 0) {
            return new self($this->textureRepository, 1, $this);
        }
        if ($this->index < 5) {
            $random = random_int(1, 100);
            if ($random < 80) {
                return new self($this->textureRepository, $this->index + 1, $this);
            }
            if ($random < 88) {
                return new Grass($this->textureRepository, random_int(0, 6), $this);
            }
            if ($random < 94) {
                return new Rail($this->textureRepository, 0, $this);
            }

            return new Pavement($this->textureRepository, 0, $this);
        }

        $random = random_int(1, 100);
        if ($random < 60) {
            return new Grass($this->textureRepository, random_int(0, 6), $this);
        }
        if ($random < 90) {
            return new Rail($this->textureRepository, 0, $this);
        }

        return new Pavement($this->textureRepository, 0, $this);
    }

    public function checkCollision(Bunner $bunner)
    {
        $checks = [[-Row::ROW_HEIGHT, Car::SOUND_ZOOM], [0, Car::SOUND_HONK], [Row::ROW_HEIGHT, Car::SOUND_ZOOM]];
        foreach ($checks as $check) {
            [$yOffset, $carSoundName] = $check;
            # Is the player on the appropriate row?
            if ($bunner && $bunner->sprite->y === $this->sprite->y + $yOffset) {
                foreach ($this->children as $child) {
                    if ($child instanceof Car) {
                        // The car must be within 100 pixels of the player on the x-axis, and moving towards the player
                        // child_obj.dx < 0 is True or False depending on whether the car is moving left or right, and
                        // dx < 0 is True or False depending on whether the player is to the left or right of the car.
                        // If the results of these two comparisons are different, the car is moving towards the player.
                        // Also, for the zoom sound, the car must be travelling faster than one pixel per frame
                        $dx = $child->x - game.bunner.x;
                        if (abs($dx) < 100 && (($child->dx < 0) != ($dx < 0))
                            && ($yOffset == 0 || abs($child->dx) > 1)) {
                            $child->playSound($carSoundName);
                        }
                    }
                }
            }
        }
    }

    public function check_collision(float $x): string
    {
        if ($this->collide($x)) {
            $this->playSound("splat0");

            return PlayerState::SPLAT;
        }

        return PlayerState::ALIVE;
    }

    public function playSound(string $sound): void
    {
        //game.play_sound("road", 1);
    }
}
