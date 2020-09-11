<?php

namespace PhpGame;

use InvalidArgumentException;

class Animator
{
    private TextureRepository $textureRepository;
    protected string $image;
    private array $boolParams = [];
    private array $floatParams = [];
    private array $intParams = [];
    protected array $acceptedParams = [];
    protected Sprite $sprite;

    public function __construct(TextureRepository $textureRepository, Sprite $sprite = null, string $defaultImage = 'default.png')
    {
        $this->image = $defaultImage;
        $this->textureRepository = $textureRepository;
        $this->sprite = $sprite ?? new Sprite($textureRepository[$defaultImage]);
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
        if (!in_array($key, $this->acceptedParams, true)) {
            throw new InvalidArgumentException("Parameter ".$key." does not exists for animated sprite ".static::class);
        }
    }

    public function getSprite(): Sprite
    {
        return $this->sprite;
    }

    public function __clone()
    {
        $this->sprite = clone $this->sprite;
    }


}
