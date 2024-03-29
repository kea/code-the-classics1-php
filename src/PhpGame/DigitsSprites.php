<?php

namespace PhpGame;

use PhpGame\SDL\Renderer;
use PhpGame\SDL\Texture;

class DigitsSprites
{
    /** @var array<int, Texture> */
    private array $textures;
    private string $text;
    private Transform $transform;
    private Anchor $anchor;
    private int $digitWidth;
    private int $digitHeight;
    private int $tracking = 0;

    /**
     * Sprite constructor.
     * @param array<int, Texture> $textures
     * @param string              $text
     */
    public function __construct(array $textures, string $text, Vector2Float $position)
    {
        if (count($textures) !== 10) {
            throw new \RuntimeException(sprintf("Digits need 10 textures, %d passed", count($textures)));
        }
        $this->textures = $textures;
        $this->text = $text;
        $this->transform = new Transform($position);
        $this->anchor = Anchor::LeftTop();
        $this->digitWidth = $this->textures[0]->getWidth();
        $this->digitHeight = $this->textures[0]->getWidth();
    }

    public function setTracking(int $tracking): void
    {
        $this->tracking = $tracking;
    }

    public function setAnchor(Anchor $anchor): void
    {
        $this->anchor = $anchor;
    }

    /**
     * @param array<int, string> $paths
     * @param Renderer           $renderer
     * @return static
     */
    public static function fromImages(array $paths, Renderer $renderer): self
    {
        $textures = [];
        foreach ($paths as $path) {
            $textures[] = Texture::loadFromFile($path, $renderer);
        }

        return new self($textures, '', Vector2Float::zero());
    }

    public function updateText(string $text): void
    {
        $this->text = $text;
    }

    public function setPosition(Vector2Float $position): void
    {
        $this->transform->setPosition($position);
    }

    public function draw(Renderer $renderer): void
    {
        $chars = str_split($this->text);

        $digitRealWidth = $this->digitWidth + $this->tracking;
        $displacement = new Vector2Float($digitRealWidth, 0);

        $boundedRect = $this->anchor->getBoundedRect(
            $this->transform->getPosition()->x,
            $this->transform->getPosition()->y,
            $digitRealWidth * count($chars),
            $this->digitHeight
        );

        $nextPosition = new Vector2Float($boundedRect->x, $boundedRect->y);

        foreach ($chars as $char) {
            $digitIndex = max(0, min(9, ord($char) - ord('0')));
            $renderer->drawTexture(
                $this->textures[$digitIndex],
                new \SDL_Rect((int)$nextPosition->x, (int)$nextPosition->y, $this->digitWidth, $this->digitHeight)
            );
            $nextPosition = $nextPosition->add($displacement);
        }
    }
}
