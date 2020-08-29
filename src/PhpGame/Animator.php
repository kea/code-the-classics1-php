<?php

namespace PhpGame;

use PhpGame\SDL\Renderer;
use PhpGame\SDL\Texture;

class Animator
{
    private TextureRepository $textureRepository;
    protected string $image;
    private array $boolParams = [];
    private array $floatParams = [];
    private array $intParams = [];
    protected array $acceptedParams = [];
    private Sprite $sprite;

    public function __construct(TextureRepository $textureRepository, Sprite $sprite, string $defaultImage = 'default.png')
    {
        $this->image = $defaultImage;
        $this->textureRepository = $textureRepository;
        $this->sprite = $sprite;
    }

    public function update(float $deltaTime): void
    {
        $this->sprite->updateTexture($this->textureRepository[$this->image]);
    }

    public function setBool(string $key, bool $value): void
    {
        $this->assertIsValidParam($key);
        $this->boolParams[$key] = $value;
    }

    public function setFloat(string $key, float $value): void
    {
        $this->assertIsValidParam($key);
        $this->floatParams[$key] = $value;
    }

    public function setInt(string $key, int $value): void
    {
        $this->assertIsValidParam($key);
        $this->intParams[$key] = $value;
    }

    public function getBool(string $key): bool
    {
        return $this->boolParams[$key];
    }

    public function getFloat(string $key): float
    {
        return $this->floatParams[$key];
    }

    public function getInt(string $key): int
    {
        return $this->intParams[$key];
    }

    private function assertIsValidParam(string $key): void
    {
        if (!isset($this->acceptedParams[$key])) {
            throw new \InvalidArgumentException("Parameter ".$key." does not exists for animated sprite ".static::class);
        }
    }
}
