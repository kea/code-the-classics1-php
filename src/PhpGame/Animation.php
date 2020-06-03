<?php

namespace PhpGame;

use PhpGame\SDL\Screen;

class Animation implements DrawableInterface
{
    /** @var array<string> */
    private array $images;
    private int $framePerSecond;
    private float $elapsedTime;
    private int $frames;
    private bool $loop;
    private bool $isRunning = false;

    /**
     * Animation constructor.
     * @param array<string> $images
     * @param int           $framePerSecond
     * @param bool          $loop
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
        $this->elapsedTime = 0;
        $this->isRunning = true;
    }

    private function getCurrentFrameNumber(): int
    {
        $frames = floor($this->elapsedTime * $this->framePerSecond);

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

    public function update(float $deltaTime): void
    {
        $this->elapsedTime += $deltaTime;
    }

    public function draw(Screen $screen): void
    {
        $screen->drawImage($this->getCurrentFrame(), 130, 280, 540, 90);
    }
}