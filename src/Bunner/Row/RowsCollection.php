<?php

namespace Bunner\Row;

use Bunner\EntityRegistry;
use Bunner\Game;
use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundManager;
use PhpGame\TextureRepository;
use PhpGame\TimeUpdatableInterface;
use PhpGame\Vector2Float;

class RowsCollection implements DrawableInterface, TimeUpdatableInterface, \IteratorAggregate
{
    /** @var array<Row> */
    private array $rows = [];
    private TextureRepository $textureRepository;
    private SoundManager $soundManager;
    private EntityRegistry $entityRegistry;

    public function __construct(TextureRepository $textureRepository, SoundManager $soundManager, EntityRegistry $entityRegistry)
    {
        $this->textureRepository = $textureRepository;
        $this->soundManager = $soundManager;
        $this->entityRegistry = $entityRegistry;
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
            $grass = new Grass($this->textureRepository, 0);
            $this->rows[] = $grass;
            $this->entityRegistry->add($grass);

            return;
        }

        $nextRow = $this->rows[$rowsCount-1]->nextRow();
        if ($nextRow instanceof SoundEmitterInterface) {
            $nextRow->setSoundManager($this->soundManager);
        }
        $this->rows[] = $nextRow;
        $this->entityRegistry->add($nextRow);
    }

    public function update(float $deltaTime): void
    {
        foreach ($this->rows as $row) {
            $row->update($deltaTime);
        }
    }

    public function draw(Renderer $renderer): void
    {
//        $reverseRows = array_reverse($this->rows);
//        foreach ($reverseRows as $row) {
//            $row->draw($renderer);
//        }
    }

    /** @return \ArrayIterator<Row> */
    public function getIterator()
    {
        return new \ArrayIterator($this->rows);
    }

    public function updateVerticalScroll(float $verticalScroll): void
    {
        if (end($this->rows)->contains(new Vector2Float(Game::WIDTH / 2, $verticalScroll - Game::HEIGHT / 2))) {
            $this->createRow();
        }
    }
}
