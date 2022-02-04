<?php

declare(strict_types=1);

namespace Bunner;

final class Score
{
    private int $score = 0;
    private int $maxScore = 0;
    private string $storeName = 'score.txt';

    public function getScore(): int
    {
        return $this->score;
    }

    public function getMaxScore(): int
    {
        return $this->maxScore;
    }

    public function updateScore(int $score): void
    {
        $this->score = max($this->score, $score);
        $this->maxScore = max($this->score, $this->maxScore);
    }

    public function resetScore(): void
    {
        $this->score = 0;
    }

    public function loadMaxScore(): void
    {
        $filename = $this->getStoreFullpath();
        if (!file_exists($filename)) {
            $this->maxScore = 0;

            return;
        }

        $this->maxScore = (int)file_get_contents($filename);
    }

    private function getStoreFullpath(): string
    {
        return __DIR__.'/'.$this->storeName;
    }

    public function saveMaxScore(): void
    {
        file_put_contents($this->getStoreFullpath(), (string)$this->maxScore);
    }
}
