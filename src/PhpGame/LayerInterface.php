<?php

namespace PhpGame;

interface LayerInterface
{
    public const DEFAULT = 'default';
    public const UI = 'ui';

    public function isOnLayer(string $layer): bool;
}
