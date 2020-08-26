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

    public function offsetGet($index)
    {
        if (!isset($this->textures[$index])) {
            $fullPath = $this->basePath.$index;
            $this->textures[$index] = Texture::loadFromFile($fullPath, $this->renderer);
        }

        return $this->textures[$index];
    }


    public function offsetExists($offset)
    {
        return isset($this->textures[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Texture) {
            throw new \InvalidArgumentException("Only ".Texture::class." is allowed");
        }
        $this->textures[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->textures[$offset]);
    }
}
