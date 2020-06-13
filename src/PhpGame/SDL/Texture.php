<?php

namespace PhpGame\SDL;

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
     * @param Renderer $renderer
     * @return Texture
     */
    public static function loadFromFile(string $imageFile, Renderer $renderer): Texture
    {
        $texture = new self();

        $texture->SDLTexture = $renderer->loadTexture($imageFile);

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