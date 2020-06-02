<?php

namespace PhpGame\SDL;

use PhpGame\AssetLoadException;

class Texture
{
    /** @var resource */
    private $SDLTexture;

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
     * @param resource $renderer
     * @return Texture
     */
    public static function loadFromFile(string $imageFile, $renderer): Texture
    {
        $texture = new self();

        $texture->SDLTexture = \IMG_LoadTexture($renderer, $imageFile);

        return $texture;
    }

    /**
     * @return resource
     */
    public function getContent()
    {
        return $this->SDLTexture;
    }
}