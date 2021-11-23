<?php

namespace PhpGame;

use PhpGame\SDL\Renderer;
use PhpGame\SDL\Texture;

class TextSprite
{
    private Texture $texture;
    private string $text;

    /**
     * Sprite constructor.
     * @param Texture $texture
     * @param string  $text
     */
    public function __construct(Texture $texture, string $text, Vector2FLoat $position)
    {
        $this->texture = $texture;
        $this->text = $text;
        $this->transform = new Transform($position);
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
        $this->updateBoundedRect();
    }

    public function draw(Renderer $renderer): void
    {
        $chars = str_split($this->text);
        $nextPosition = clone $this->transform->getPosition();
        foreach ($chars as $char) {
            $positionInSprite = ord($char) - ord('0');
            $boundedCharRect = new \SDL_Rect(28 * $positionInSprite, 0, 28, 28);
            $destinationRect = new \SDL_Rect($nextPosition->x, $nextPosition->y, 28, 28);
            $nextPosition = $nextPosition->add(new Vector2Float(28, 0));
            $renderer->drawPartialTexture($this->texture, $destinationRect, $boundedCharRect);
        }
    }

    private function updateBoundedRect(): void
    {
        //$width = $this->texture->getWidth() * $this->transform->getScale()->x;
        $width = $this->texture->getHeight() * strlen($this->text) * $this->transform->getScale()->x;
        $height = $this->texture->getHeight() * $this->transform->getScale()->y;

        $this->boundedRect = $this->anchor->getBoundedRect(
            $this->transform->getPosition()->x,
            $this->transform->getPosition()->y,
            $width,
            $height
        );
    }
}
