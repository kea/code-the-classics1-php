<?php

namespace PhpGame\ECS;

class Entity
{
    private int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public static function fromString(string $id): Entity
    {
        return new self((int)$id);
    }

    public function __toString()
    {
        return (string)$this->id;
    }
}