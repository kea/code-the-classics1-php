<?php

namespace PhpGame;

use PhpGame\SDL\Renderer;
use PhpGame\SDL\Texture;

class TextSprite
{
    private Texture $texture;
    private string $text;
    private Transform $transform;
    private Anchor $anchor;

    /**
     * Sprite constructor.
     * @param Texture $texture
     * @param string  $text
     */
    public function __construct(Texture $texture, string $text, Vector2Float $position)
    {
        $this->texture = $texture;
        $this->text = $text;
        $this->transform = new Transform($position);
        $this->anchor = Anchor::CenterBottom();
    }

    public static function fromImage(string $path, Renderer $renderer): self
    {
        $texture = Texture::loadFromFile($path, $renderer);

        return new self($texture, '', Vector2Float::zero());
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
        $nextPosition = clone $this->transform->getPosition();
        foreach ($chars as $char) {
            $positionInSprite = ord($char) - ord('0');
            $boundedCharRect = new \SDL_Rect(28 * $positionInSprite, 0, 28, 28);
            $destinationRect = new \SDL_Rect((int)$nextPosition->x, (int)$nextPosition->y, 28, 28);
            $nextPosition = $nextPosition->add(new Vector2Float(28, 0));
            $renderer->drawPartialTexture($this->texture, $destinationRect, $boundedCharRect);
        }
    }
}
