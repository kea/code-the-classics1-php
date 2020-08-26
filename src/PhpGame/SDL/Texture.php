<?php

namespace PhpGame\SDL;

class Texture
{
    /** @var resource */
    private $SDLTexture;
    private int $width = 0;
    private int $height = 0;

    private function __construct()
    {
    }

    public function __destruct()
    {
        if (is_resource($this->SDLTexture)) {
            SDL_DestroyTexture($this->SDLTexture);
        }
    }

    /**
     * @param string   $imageFile
     * @param Renderer $renderer
     * @return Texture
     */
    public static function loadFromFile(string $imageFile, Renderer $renderer): Texture
    {
        $texture = new self();

        $texture->SDLTexture = IMG_LoadTexture($renderer->getSDLRenderer(), $imageFile);
        $notImportant = 0;
        SDL_QueryTexture($texture->SDLTexture, $notImportant, $notImportant, $texture->width, $texture->height);

        return $texture;
    }

    /**
     * @return resource
     */
    public function getContent()
    {
        return $this->SDLTexture;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}
