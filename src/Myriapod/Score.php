<?php

declare(strict_types=1);

namespace Myriapod;

final class Score
{
    private int $score = 0;

    public function get(): int
    {
        return $this->score;
    }

    public function add(int $score): void
    {
        $this->score = max($this->score, $score);
    }

    public function reset(): void
    {
        $this->score = 0;
    }
}
