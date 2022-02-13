<?php

declare(strict_types=1);

namespace Myriapod\Enemy;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\Sprite;
use PhpGame\TextureRepository;
use PhpGame\TimeUpdatableInterface;
use PhpGame\Vector2Float;

class Segment implements DrawableInterface, TimeUpdatableInterface
{
    private const SPEED = 1;
    public const DIRECTION_UP = 0;
    public const DIRECTION_RIGHT = 1;
    public const DIRECTION_DOWN = 2;
    public const DIRECTION_LEFT = 3;
    private const DX = [0, 1, 0, -1];
    private const DY = [-1, 0, 1, 0];
    private const SECONDARY_AXIS_SPEED = [0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1 , 1, 2, 2, 2, 2];
    private const SECONDARY_AXIS_POSITIONS = [0, 0, 0, 0, 0, 1, 2, 3, 4, 5, 6, 7, 8, 10, 12, 14];

    private Sprite $sprite;
    private float $time = .0;
    private int $direction = self::DIRECTION_RIGHT;
    private int $inEdge = self::DIRECTION_LEFT;
    private int $outEdge = self::DIRECTION_RIGHT;
    private int $disallowDirection = self::DIRECTION_UP;
    private int $previousXDirection = self::DIRECTION_RIGHT;
    private int $yLimit;
    private int $phase = -1;

    public function __construct(
        private TextureRepository $textureRepository,
        private int $cellX,
        private int $cellY,
        private int $health,
        private bool $fast,
        private bool $head,
        private SegmentPositions $segmentsPosition,
        private Rocks $rocks,
        bool $hasPlayer = false
    ) {
        $this->sprite = new Sprite(
            $this->textureRepository['seg00120.png'],
            $this->cellX * 32 + 16,
            $this->cellY * 32 + 16
        );
        $this->yLimit = $hasPlayer ? 18 : 0;
    }

    public function isAlive(): bool
    {
        return $this->health > 0;
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->draw($renderer);
    }

    public function update(float $deltaTime): void
    {
        $this->time += $deltaTime;

        $phaseDuration = 1 / 60;
        $phaseCycleDuration = 16 * $phaseDuration;
        if ($this->time >= $phaseCycleDuration) {
            $this->time -= $phaseCycleDuration;
        }

        $phase = (int)floor($this->time / ($phaseDuration));

        if ($this->phase === $phase) {
            return;
        }
        if ($this->phase > $phase) {
            $phase = 0;
        } elseif ($this->phase === 3) {
            $phase = 4;
        }

        $this->phase = $phase;

        if ($phase === 0) {
            $this->phase0();
        } elseif ($phase === 4) {
            $this->phase4();
        }

        // Turn 90 degrees: 1 = anti-clockwise turn, 2 = straight ahead, 3 = clockwise turn
        $turnIdx = $this->outEdge - $this->inEdge % 4;
        $turnIdx = ($turnIdx >= 0) ?  $turnIdx : $turnIdx + 4;

        $offsetX = self::SECONDARY_AXIS_POSITIONS[$phase] * (2 - $turnIdx);
        $stolenYMovement = ($turnIdx % 2) * self::SECONDARY_AXIS_POSITIONS[$phase];
        $offsetY = -16 + ($phase * 2) - $stolenYMovement;

        $rotationMatrix = match ($this->inEdge) {
            0 => [1, 0, 0, 1],
            1 => [0, -1, 1, 0],
            2 => [-1, 0, 0, -1],
            3 => [0, 1, -1, 0],
        };

        [$offsetX, $offsetY] = [
            $offsetX * $rotationMatrix[0] + $offsetY * $rotationMatrix[1],
            $offsetX * $rotationMatrix[2] + $offsetY * $rotationMatrix[3],
        ];

        $pos = $this->cell2pos($this->cellX, $this->cellY, $offsetX, $offsetY);
        $this->sprite->setPosition(new Vector2Float($pos[0], $pos[1]));

        $fast = $this->fast ? '1' : '0';
        $health = $this->health === 2 ? '1' : '0';
        $head = $this->head ? '1' : '0';
        $direction = ((self::SECONDARY_AXIS_SPEED[$phase] * ($turnIdx - 2)) + ($this->inEdge * 2) + 4) % 8;
        $legFrame = intdiv($phase, 4);
        $image = $fast.$health.$head.$direction.$legFrame;
        $this->sprite->updateTexture($this->textureRepository['seg'.$image.'.png']);
    }

    public function getPosition(): Vector2Float
    {
        return $this->sprite->getPosition();
    }

    private function isHorizontal(int $dir): bool
    {
        return $dir === self::DIRECTION_LEFT || $dir === self::DIRECTION_RIGHT;
    }

    private function inverseDirection(int $dir): int
    {
        return match ($dir) {
            self::DIRECTION_UP => self::DIRECTION_DOWN,
            self::DIRECTION_RIGHT => self::DIRECTION_LEFT,
            self::DIRECTION_DOWN => self::DIRECTION_UP,
            self::DIRECTION_LEFT => self::DIRECTION_RIGHT,
        };
    }

    public function pos2cell(): array
    {
        $position = $this->sprite->getPosition();

        return [intdiv((int)$position->x - 16, 32), intdiv((int)$position->y, 32)];
    }

    private function cell2pos(int $cellX, int $cellY, int $xOffset = 0, int $yOffset = 0): array
    {
        return [($cellX * 32) + 32 + $xOffset, ($cellY * 32) + 16 + $yOffset];
    }

    private function nextEdge(): int
    {
        $movesIndex = array_map($this->movePriority(...), [self::DIRECTION_UP, self::DIRECTION_RIGHT, self::DIRECTION_DOWN, self::DIRECTION_LEFT]);
        $betterMove = min($movesIndex);

        return array_search($betterMove, $movesIndex, true);
    }

    public function movePriority(int $proposedOutEdge): int
    {
            $newCellX = $this->cellX + self::DX[$proposedOutEdge];
            $newCellY = $this->cellY + self::DY[$proposedOutEdge];
            $out = $newCellX < 0 || $newCellX > Rocks::WIDTH - 1 || $newCellY < 0 || $newCellY > Rocks::HEIGHT - 1;
            $turningBackOnSelf = $proposedOutEdge === $this->inEdge;
            $directionDisallowed = $proposedOutEdge === $this->disallowDirection;
            if ($out || ($newCellY === 0 && $newCellX < 0)) {
                $rockPresent = false;
            } else {
                $rockPresent = $this->rocks->isOccupied($newCellX, $newCellY);
            }
            $occupiedBySegment = $this->segmentsPosition->isOccupied($newCellY, $newCellY)
                || $this->segmentsPosition->isOccupied($this->cellX, $this->cellY, $proposedOutEdge);
            if ($rockPresent) {
                $horizontalBlocked = $this->isHorizontal($proposedOutEdge);
            } else {
                $horizontalBlocked = !$this->isHorizontal($proposedOutEdge);
            }
            $sameAsPreviousXDirection = $proposedOutEdge === $this->previousXDirection;

            $boolToInt = static fn(bool $b): int => $b ? 1 : 0;

            return
                $boolToInt($out) * 1000000 +
                $boolToInt($turningBackOnSelf) * 100000 +
                $boolToInt($directionDisallowed) * 10000 +
                $boolToInt($occupiedBySegment) * 1000 +
                $boolToInt($rockPresent) * 100 +
                $boolToInt($horizontalBlocked) * 10 +
                $boolToInt($sameAsPreviousXDirection);
    }

    /**
     * @return void
     */
    private function phase0(): void
    {
        $this->cellX += self::DX[$this->outEdge];
        $this->cellY += self::DY[$this->outEdge];

        $this->inEdge = $this->inverseDirection($this->outEdge);

        if ($this->cellY === $this->yLimit) {
            $this->disallowDirection = self::DIRECTION_UP;
        }
        if ($this->cellY === Rocks::HEIGHT - 1) {
            $this->disallowDirection = self::DIRECTION_DOWN;
        }
    }

    /**
     * @return void
     */
    private function phase4(): void
    {
        $this->outEdge = $this->nextEdge();
        if ($this->isHorizontal($this->outEdge)) {
            $this->previousXDirection = $this->outEdge;
        }
        $newCellX = $this->cellX + self::DX[$this->outEdge];
        $newCellY = $this->cellY + self::DY[$this->outEdge];

        if ($newCellX >= 0 && $newCellX < Rocks::HEIGHT) {
            $this->rocks->damage($newCellX, $newCellY, 5, false);
        }

        $this->segmentsPosition->occupy($newCellX, $newCellY);
        $this->segmentsPosition->occupy($newCellX, $newCellY, $this->inverseDirection($this->outEdge));
    }

    public function collideWith(\SDL_Rect $rect): bool
    {
        return $this->sprite->getBoundedRect()->HasIntersection($rect);
    }

    public function damage(int $damage): void
    {
        $this->health -= $damage;
    }
}
