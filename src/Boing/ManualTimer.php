<?php

namespace Boing;

class ManualTimer
{
    private float $timeLeft;
    private bool $isStarted = false;
    /**
     * @var callable|null
     */
    private $callBack;

    public function start(float $time, callable $callBack = null): void
    {
        $this->timeLeft = $time;
        $this->isStarted = true;
        $this->callBack = $callBack;
    }

    public function decreaseTime(float $timeElapsed): void
    {
        $this->timeLeft -= $timeElapsed;
        if ($this->timeLeft < 0) {
            $this->isStarted = false;
            if ($this->callBack !== null) {
                ($this->callBack)();
            }
        }
    }

    public function isStarted(): bool
    {
        return $this->isStarted;
    }
}
