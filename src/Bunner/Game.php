<?php

declare(strict_types=1);

namespace Bunner;

use Bunner\Player\Bunner;
use Bunner\Row\RowsCollection;
use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;
use PhpGame\SoundManager;
use PhpGame\TextureRepository;

class Game implements DrawableInterface, SoundEmitterInterface
{
    public const HEIGHT = 800;
    public const WIDTH = 480;

    use SoundEmitterTrait;
    protected RowsCollection $rowsCollection;
    private TextureRepository $textureRepository;
    private ?Bunner $player = null;
    private InputActions $inputActions;

    public function __construct(TextureRepository $textureRepository, SoundManager $soundManager, InputActions $inputActions)
    {
        $this->soundManager = $soundManager;
        $this->rowsCollection = new RowsCollection($textureRepository, $soundManager);
        $this->rowsCollection->createRows(24);
        $this->textureRepository = $textureRepository;
        $this->inputActions = $inputActions;
    }

    public function update(float $deltaTime): void
    {
        /* @todo
        if ($this->player !== null) {
            $this->rowsCollection->setScrollInc(
                max(1, min(3, Game::HEIGHT - $this->player->getY()) / (Game::HEIGHT / 4))
            );
            $this->updateAmbientSoundEffects();
        } else {
            $this->rowsCollection->setScrollInc(1);
        }
        */

        $this->rowsCollection->update($deltaTime);
        if ($this->player !== null) {
            $this->player->update($deltaTime);
        }
    }

    private function updateAmbientSoundEffects(): void
    {
        /* @todo
        for name, count, row_class in [("river", 2, Water), ("traffic", 3, Road)] {
            $volume = sum([16.0 / max(16.0, abs(r.y - self.bunner.y)) for r in self.rows if isinstance(r, row_class)]) - 0.2
            $volume = min(0.4, volume)
            $this->loopSound(name, count, volume);
        }
        */
    }

    public function draw(Renderer $renderer): void
    {
        $this->rowsCollection->draw($renderer);
        if ($this->player !== null) {
            $this->player->draw($renderer);
        }
    }

    public function start(): void
    {
    }

    public function isGameOver(): bool
    {
        if ($this->player === null) {
            return false;
        }

        return !$this->player->isAlive();
    }

    public function addPlayer(): void
    {
        $this->player = new Bunner($this->textureRepository, $this->inputActions, $this->rowsCollection);
        $this->player->setSoundManager($this->soundManager);
    }
}
