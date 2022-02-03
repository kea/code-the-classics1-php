<?php

namespace Bunner;

use PhpGame\Sprite;
use PhpGame\TextureRepository;

class SpriteRegistry
{
    public function __construct(private EntityRegistry $entityRegistry, private TextureRepository $textureRepository)
    {
    }

    public function add(Sprite $sprite): void
    {
        $this->entityRegistry->add($sprite);
    }
}
