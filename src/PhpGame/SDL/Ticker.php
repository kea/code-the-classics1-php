<?php

namespace PhpGame\SDL;

class Ticker
{
    private float $deltaTime;
    private float $lastTime;
    private float $elapsedTime = 0;
    private int $frame = 0;
    private float $tickPerSecond;

    public function __construct()
    {
        $this->lastTime = microtime(true);
    }

    public function tick(): float
    {
        $currentTime = microtime(true);
        $this->deltaTime = $currentTime - $this->lastTime;
        $this->lastTime = $currentTime;
        ++$this->frame;
        $this->elapsedTime += $this->deltaTime;
        if ($this->elapsedTime > 1) {
            $this->updateFps();
            $this->elapsedTime = $this->frame = 0;
        }

        return $this->deltaTime;
    }

    private function updateFps(): void
    {
        $this->tickPerSecond = round($this->frame / $this->elapsedTime, 1);
    }

    public function getTickPerSecond(): float
    {
        return $this->tickPerSecond;
    }
}