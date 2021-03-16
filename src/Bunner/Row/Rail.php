<?php

declare(strict_types=1);

namespace Bunner\Row;

use Bunner\Game;
use Bunner\Obstacle\Train;
use PhpGame\SDL\Renderer;
use PhpGame\Vector2Float;

class Rail extends Row
{
    protected string $textureName = 'rail%d.png';

    public function nextRow(): Row
    {
        if ($this->index < 3) {
            return new self($this->textureRepository, $this->index + 1, $this);
        }

        $nextPossibleClasses = [Road::class, Water::class];
        $nextClass = $nextPossibleClasses[array_rand($nextPossibleClasses)];

        return new $nextClass($this->textureRepository, 0, $this);
    }

    public function update(float $deltaTime): void
    {
        parent::update($deltaTime);

        if ($this->index !== 1) {
            return;
        }

        foreach ($this->children as $i => $child) {
            $trainX = $child->getX();
            if ($trainX < -1000 || $trainX > self::ROW_WIDTH + 1000) {
                unset($this->children[$i]);
                continue;
            }
            $child->setY($this->sprite->getPosition()->y - 13);
            $child->update($deltaTime);
        }

        // If on-screen, and there is currently no train, and with a 1% chance every frame, create a train
        $railBottom = $this->sprite->bottom();
        if ($railBottom > 20 && $railBottom < Game::HEIGHT && count($this->children) === 0 && random_int(1, 100) === 42) {
            // Randomly choose a direction for trains to move. This can be different for each train created
            $dx = random_int(0, 1) === 0 ? -20 : 20;
            $trainPosition = new Vector2Float($dx < 0 ? self::ROW_WIDTH + 1000 : -1000, $railBottom - 13);
            $this->children[] = new Train($this->textureRepository, $trainPosition, $dx);
            $this->playSound("bell0.wav");
            $this->playSound("train".random_int(0, 1).'.wav');
        }
    }

    public function draw(Renderer $renderer): void
    {
        parent::draw($renderer);
        foreach ($this->children as $child) {
            $child->draw($renderer);
        }
    }

    public function playLandedSound(): void
    {
        $this->playSound("grass0.wav");
    }
}