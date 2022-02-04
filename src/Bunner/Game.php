<?php

declare(strict_types=1);

namespace Bunner;

use Bunner\GUI\GUI;
use Bunner\Player\Bunner;
use Bunner\Row\RowsCollection;
use PhpGame\Camera;
use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use PhpGame\LayerInterface;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;
use PhpGame\SoundManager;
use PhpGame\TextureRepository;
use PhpGame\TimeUpdatableInterface;
use PhpGame\Vector2Int;

class Game implements DrawableInterface, TimeUpdatableInterface, SoundEmitterInterface
{
    public const HEIGHT = 800;
    public const WIDTH = 480;

    use SoundEmitterTrait;

    protected RowsCollection $rowsCollection;
    private TextureRepository $textureRepository;
    private ?Bunner $player = null;
    private InputActions $inputActions;
    private Camera $camera;
    private float $verticalScroll = 0.0;
    private EntityRegistry $entityRegistry;
    private GUI $gui;

    public function __construct(
        TextureRepository $textureRepository,
        SoundManager $soundManager,
        InputActions $inputActions,
        Camera $camera,
        GUI $gui,
        EntityRegistry $entityRegistry
    ) {
        $this->soundManager = $soundManager;
        $this->rowsCollection = new RowsCollection($textureRepository, $soundManager, $entityRegistry);
        $this->rowsCollection->createRows(24);
        $this->textureRepository = $textureRepository;
        $this->inputActions = $inputActions;
        $this->camera = $camera;
        $this->gui = $gui;
        $this->entityRegistry = $entityRegistry;
    }

    public function update(float $deltaTime): void
    {
        $this->rowsCollection->updateVerticalScroll($this->verticalScroll);
        $this->rowsCollection->update($deltaTime);
        $this->player?->update($deltaTime);
        $this->updateVerticalScroll($deltaTime);
        $this->camera->follow(new Vector2Int(self::WIDTH / 2, (int)$this->verticalScroll));
        $this->gui->update($deltaTime);
    }

    protected function updateVerticalScroll(float $deltaTime): void
    {
        $verticalScrollVelocity = 1;
        if ($this->player !== null) {
            $playerYOnScreen = $this->verticalScroll + (self::HEIGHT / 2) - $this->player->getY();
            $verticalScrollVelocity = max(1, min(3, 4 - round((self::HEIGHT - $playerYOnScreen) / (self::HEIGHT / 4))));
        }
        $this->verticalScroll -= $deltaTime * 60 * $verticalScrollVelocity;
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
        $grounds = array_reverse($this->entityRegistry->allByLayer(LayerInterface::DEFAULT));
        foreach ($grounds as $ground) {
            $ground->draw($renderer);
        }

        $this->player?->draw($renderer);
        $this->gui->draw($renderer);
    }

    public function start(): void
    {
        $this->verticalScroll = self::HEIGHT / 2;
    }

    public function isGameOver(): bool
    {
        if ($this->player === null) {
            return false;
        }

        return !$this->player->isAlive() && !$this->player->isAnimationPlaying();
    }

    public function addPlayer(): void
    {
        $this->player = new Bunner($this->textureRepository, $this->inputActions, $this->rowsCollection);
        if ($this->soundManager) {
            $this->player->setSoundManager($this->soundManager);
        }
        $this->entityRegistry->add($this->player);
    }
}
