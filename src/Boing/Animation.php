<?php

namespace Boing;

class Animation
{
    private array $images;
    private int $framePerSecond;
    private float $startTime;
    private int $frames;
    private bool $loop;
    private bool $isRunning = false;

    /**
     * Animation constructor.
     * @param array $images
     * @param int   $framePerSecond
     * @param bool  $loop
     */
    public function __construct(array $images, int $framePerSecond = 12, bool $loop = false)
    {
        $this->images = $images;
        $this->frames = count($images);
        $this->framePerSecond = $framePerSecond;
        $this->loop = $loop;
    }

    public function startAnimation(): void
    {
        $this->startTime = microtime(true);
        $this->isRunning = true;
    }

    private function getCurrentFrameNumber(): int
    {
        $elapsedTime = microtime(true) - $this->startTime;
        $frames = floor($elapsedTime * $this->framePerSecond);

        if (!$this->loop && $frames >= $this->frames) {
            $this->isRunning = false;

            return $this->frames - 1;
        }

        return $frames % $this->framePerSecond;
    }

    public function isRunning(): bool
    {
        return $this->isRunning;
    }

    public function getCurrentFrame(): string
    {
        return $this->images[$this->getCurrentFrameNumber()];
    }
}