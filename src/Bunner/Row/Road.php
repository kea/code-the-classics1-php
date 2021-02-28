<?php

namespace Bunner\Row;

use Bunner\Obstacle\Car;
use Bunner\Player\Bunner;
use Bunner\Player\PlayerState;
use PhpGame\TextureRepository;

class Road extends ActiveRow
{
    protected string $textureName = 'road%d.png';
    protected string $childType = Car::class;
    protected ?Bunner $player = null;

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
                return new Grass($this->textureRepository, random_int(0, 5), $this);
            }
            if ($random < 94) {
                return new Rail($this->textureRepository, 0, $this);
            }

            return new Pavement($this->textureRepository, 0, $this);
        }

        $random = random_int(1, 100);
        if ($random < 60) {
            return new Grass($this->textureRepository, random_int(0, 5), $this);
        }
        if ($random < 90) {
            return new Rail($this->textureRepository, 0, $this);
        }

        return new Pavement($this->textureRepository, 0, $this);
    }

    public function setPlayer(Bunner $bunner): void
    {
        $this->player = $bunner;
    }

    public function update(float $deltaTime): void
    {
        parent::update($deltaTime);

        $player = $this->player;
        $checks = [[-Row::ROW_HEIGHT, Car::SOUND_ZOOM], [0, Car::SOUND_HONK], [Row::ROW_HEIGHT, Car::SOUND_ZOOM]];
        foreach ($checks as $check) {
            [$yOffset, $carSoundName] = $check;
            if ($player !== null && round($player->getY()) === round($this->sprite->getPosition()->y + $yOffset)) {
                foreach ($this->children as $child) {
                    if ($child instanceof Car) {
                        $dx = $child->getX() - $player->getX();
                        if (abs($dx) < 100 && (($child->getDx() < 0) !== ($dx < 0))
                            && ($yOffset === 0 || abs($child->getDx()) > 1)) {
                            $child->playSound($carSoundName);
                        }
                    }
                }
            }
        }
    }

    public function checkCollision(Bunner $player): string
    {
        foreach ($this->children as $child) {
            if (SDL_HasIntersection($player->getSprite()->getBoundedRect(), $child->getSprite()->getBoundedRect())) {
                $this->playSound("splat0");

                return PlayerState::SPLAT;
            }
        }

        return PlayerState::ALIVE;
    }

    public function playSound(string $sound): void
    {
        //game.play_sound("road", 1);
    }
}
