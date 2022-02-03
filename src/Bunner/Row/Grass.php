<?php

declare(strict_types=1);

namespace Bunner\Row;

use Bunner\Obstacle\Hedge;
use PhpGame\SDL\Renderer;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

class Grass extends Row
{
    private const HEDGE_MIDDLE1 = 3;
    private const HEDGE_MIDDLE2 = 4;
    private const HEDGE_MIDDLE3 = 5;
    private const HEDGE_LEFT = 1;
    private const HEDGE_RIGHT = 2;
    private const ROW_BOTTOM = 0;
    private const ROW_TOP = 1;
    protected string $textureName = 'grass%d.png';
    private ?int $hedgeRowIndex = null;
    private ?array $hedgeMask = null;

    public function __construct(TextureRepository $textureRepository, int $index, ?Row $previous = null)
    {
        parent::__construct($textureRepository, $index, $previous);

        if (!$previous instanceof Grass || $previous->hedgeRowIndex === null) {
            if (random_int(1, 100) <= 50 && $index > 7 && $index < 14) {
                $this->hedgeMask = $this->generateHedgeMask();
                $this->hedgeRowIndex = self::ROW_BOTTOM;
            }
        } elseif ($previous->hedgeRowIndex === self::ROW_BOTTOM) {
            $this->hedgeMask = $previous->hedgeMask;
            $this->hedgeRowIndex = self::ROW_TOP;
        }

        if ($this->hedgeRowIndex === null) {
            return;
        }

        $previousMidSegment = null;
        for ($i = 1; $i < 13; $i++) {
            [$bushPosition, $previousMidSegment] = $this->classifyHedgeSegment($i, $previousMidSegment);
            if ($bushPosition !== null) {
                $this->children[] = new Hedge(
                    $textureRepository,
                    $bushPosition,
                    $this->hedgeRowIndex,
                    new Vector2Float($i * 40.0 - 20.0, .0)
                );
            }
        }
    }

    public function nextRow(): Row
    {
        $nextRowClass = Grass::class;
        $index = 0;
        if ($this->index > 14) {
            $nextPossibleClasses = [Road::class, Water::class];
            $nextRowClass = $nextPossibleClasses[array_rand($nextPossibleClasses)];
        } elseif ($this->index <= 5) {
            $index = $this->index + 8;
        } elseif ($this->index === 6) {
            $index = 7;
        } elseif ($this->index === 7) {
            $index = 15;
        } else {
            $index = $this->index + 1;
        }

        return new $nextRowClass($this->textureRepository, $index, $this);
    }

    private function generateHedgeMask(): array
    {
        $mask = [];
        for ($i = 0; $i<12; $i++) {
            $mask[$i] = random_int(1, 100) === 42;
        }
        $mask[random_int(0, 10)] = true;
        $resultMask = [];
        for ($i = 0; $i<12; $i++) {
            $resultMask[$i] = $mask[max(0, $i-1)] || $mask[$i] || $mask[min($i+1, 11)];
        }

        return array_merge([$resultMask[0]], $resultMask, [$resultMask[11], $resultMask[11]]);
    }

    private function classifyHedgeSegment(int $index, ?int $previousMidSegment): array
    {
        $mask = array_slice($this->hedgeMask, $index - 1, 4);

        if ($mask[1]) {
            return [null, null];
        }

        if ($mask[0] || $mask[2]) {
            return [$mask[0] ? self::HEDGE_LEFT : self::HEDGE_RIGHT, null];
        }

        if ($previousMidSegment === self::HEDGE_MIDDLE2 && $mask[3]) {
            return [self::HEDGE_MIDDLE3, null];
        }

        if ($previousMidSegment === self::HEDGE_MIDDLE1) {
            return [self::HEDGE_MIDDLE2, self::HEDGE_MIDDLE2];
        }

        return [self::HEDGE_MIDDLE1, self::HEDGE_MIDDLE1];
    }

    public function draw(Renderer $renderer): void
    {
        parent::draw($renderer);
        foreach ($this->children as $child) {
            $child->draw($renderer);
        }
    }

    /** @toto check if can be removed */
    public function update(float $deltaTime): void
    {
        parent::update($deltaTime);
        foreach ($this->children as $child) {
            $child->setY($this->sprite->getPosition()->y);
        }
    }

    public function playLandedSound(): void
    {
        $this->playSound("grass0.wav");
    }

    public function allowMovement(float $x): bool
    {
        return parent::allowMovement($x) && !$this->collide($x, 8);
    }
}
