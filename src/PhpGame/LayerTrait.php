<?php

namespace PhpGame;

trait LayerTrait
{
    protected string $layer = LayerInterface::DEFAULT;

    public function isOnLayer(string $layer): bool
    {
        return $this->layer === $layer;
    }
}