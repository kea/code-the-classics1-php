<?php

namespace Bunner;

use PhpGame\DrawableInterface;
use PhpGame\LayerInterface;

class EntityRegistry
{
    private array $entities = [];

    public function add(DrawableInterface $entity): void // @todo create Entity interface
    {
        $this->entities[] =  $entity;
    }

    public function remove(DrawableInterface $entity): void // @todo create Entity interface
    {
        $index = array_search($entity, $this->entities, true);

        if ($index === false) {
            return;
        }

        unset($this->entities[$index]);
    }

    public function all(): array
    {
        return $this->entities;
    }

    public function allByLayer(string $layer): array
    {
        return array_filter($this->entities, static fn($e) => $e instanceof LayerInterface && $e->isOnLayer($layer));
    }

    public function allByInterface(string $interface): array
    {
        return array_filter($this->entities, static fn($e) => $e instanceof $interface);
    }
}