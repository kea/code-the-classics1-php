<?php

namespace Cavern;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class RobotCollection implements IteratorAggregate, Countable
{
    /** @var Robot[] */
    private array $robots = [];

    public function reset(): void
    {
        unset($this->robots);
        $this->robots = [];
    }

    public function add(Robot $robot): void
    {
        $this->robots[] = $robot;
    }

    public function removeNotActive(): void
    {
        $this->robots = array_filter($this->robots, fn($robot) => $robot->isActive());
    }

    /**
     * @return ArrayIterator<int, Robot>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->robots);
    }

    public function isEmpty(): bool
    {
        return empty($this->robots);
    }

    public function count(): int
    {
        return count($this->robots);
    }
}
