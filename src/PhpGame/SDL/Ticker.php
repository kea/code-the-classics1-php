<?php

namespace PhpGame\SDL;

class Ticker
{
    private float $deltaTime;
    private float $lastTime;
    private float $elapsedTime = 0;
    private int $frame = 0;

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
        if ($this->frame === 60) {
            $this->log();
            $this->elapsedTime = $this->frame = 0;
        }

        return $this->deltaTime;
    }

    private function log(): void
    {
        echo "\nFPS: ".round($this->frame / $this->elapsedTime);
    }
}