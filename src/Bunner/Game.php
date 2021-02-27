<?php

declare(strict_types=1);

namespace Bunner;

use Bunner\Row\RowsCollection;
use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;
use PhpGame\SoundManager;
use PhpGame\TextureRepository;

class Game implements DrawableInterface, SoundEmitterInterface
{
    public const HEIGHT = 800;

    use SoundEmitterTrait;
    protected RowsCollection $rowsCollection;
    private TextureRepository $textureRepository;
    private ?SoundManager $soundManager = null;

    public function __construct(TextureRepository $textureRepository, SoundManager $soundManager)
    {
        $this->soundManager = $soundManager;
        $this->rowsCollection = new RowsCollection($textureRepository, $soundManager);
        $this->rowsCollection->createRows(23);
        $this->textureRepository = $textureRepository;
    }

    public function update(float $deltaTime): void
    {
        $this->rowsCollection->update($deltaTime);
    }

    public function draw(Renderer $renderer): void
    {
        $this->rowsCollection->draw($renderer);
    }

    public function start(): void
    {
    }

    public function isGameOver(): bool
    {
        return false;
    }
}
