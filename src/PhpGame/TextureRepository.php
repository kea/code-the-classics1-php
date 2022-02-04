<?php

declare(strict_types=1);

namespace PhpGame;

use PhpGame\SDL\Renderer;
use PhpGame\SDL\Texture;

class TextureRepository implements \ArrayAccess
{
    private array $textures = [];
    private Renderer $renderer;
    private string $basePath;

    public function __construct(Renderer $renderer, string $basePath)
    {
        $this->renderer = $renderer;
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    public function preload(string $imageName): void
    {
        if (!$this->offsetGet($imageName)) {
            throw new \RuntimeException("Image not found ".$imageName);
        }
    }

    public function offsetGet(mixed $offset): mixed
    {
        if (!isset($this->textures[$offset])) {
            $fullPath = $this->basePath.$offset;
            $this->textures[$offset] = Texture::loadFromFile($fullPath, $this->renderer);
        }

        return $this->textures[$offset];
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->textures[$offset]);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!$value instanceof Texture) {
            throw new \InvalidArgumentException("Only ".Texture::class." is allowed");
        }
        $this->textures[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->textures[$offset]);
    }
}
