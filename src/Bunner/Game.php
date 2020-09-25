<?php

declare(strict_types=1);

namespace Bunner;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;
use PhpGame\TextureRepository;

class Game implements DrawableInterface, SoundEmitterInterface
{
    use SoundEmitterTrait;
    protected RowsCollection $rowsCollection;
    private TextureRepository $textureRepository;

    public function __construct(TextureRepository $textureRepository)
    {
        $this->rowsCollection = new RowsCollection($textureRepository);
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
