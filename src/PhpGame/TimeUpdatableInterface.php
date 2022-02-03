<?php

declare(strict_types=1);

namespace PhpGame;

interface  TimeUpdatableInterface
{
    public function update(float $deltaTime): void;
}
