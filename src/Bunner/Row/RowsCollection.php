<?php

namespace Bunner\Row;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundManager;
use PhpGame\TextureRepository;

class RowsCollection implements DrawableInterface
{
    private array $rows = [];
    private TextureRepository $textureRepository;
    private float $newRowTimer = 0.0;
    private SoundManager $soundManager;

    public function __construct(TextureRepository $textureRepository, SoundManager $soundManager)
    {
        $this->textureRepository = $textureRepository;
        $this->soundManager = $soundManager;
    }

    public function createRows(int $int = 1): void
    {
        for ($i = 0; $i < $int; ++$i) {
            $this->createRow();
        }
    }

    public function createRow(): void
    {
        $rowsCount = count($this->rows);
        if ($rowsCount === 0) {
            echo "First row\n";
            $this->rows[] = new Grass($this->textureRepository, 0);

            return;
        }

        echo "$rowsCount row\n";
        $nextRow = $this->rows[$rowsCount-1]->nextRow();
        if ($nextRow instanceof SoundEmitterInterface) {
            $nextRow->setSoundManager($this->soundManager);
        }
        $this->rows[] = $nextRow;
    }

    public function update(float $deltaTime): void
    {
        $this->newRowTimer += $deltaTime;
        if ($this->newRowTimer >= 2.0) {
            $this->newRowTimer -= 2.0;
            $this->createRows(3);
        }
        foreach ($this->rows as $row) {
            $row->update($deltaTime);
        }
    }

    public function draw(Renderer $renderer): void
    {
        $reverseRows = array_reverse($this->rows);
        foreach ($reverseRows as $row) {
            $row->draw($renderer);
        }
    }
}
