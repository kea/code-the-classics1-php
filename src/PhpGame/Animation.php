<?php

namespace PhpGame;

use PhpGame\SDL\Texture;

class Animation implements TimeUpdatableInterface
{
    /** @var array<string|Texture> */
    private array $images;
    private int $framePerSecond;
    private float $elapsedTime = 0;
    private int $framesCount;
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
        $this->framesCount = count($images);
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

        if (!$this->loop && $frames >= $this->framesCount) {
            $this->isRunning = false;

            return $this->framesCount - 1;
        }

        return $frames % $this->framesCount;
    }

    public function isRunning(): bool
    {
        return $this->isRunning;
    }

    public function getCurrentFrame() //:string|Texture
    {
        return $this->images[$this->getCurrentFrameNumber()];
    }

    public function update(float $deltaTime): void
    {
        $this->elapsedTime += $deltaTime;
    }
}