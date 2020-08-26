<?php

namespace PhpGame;

use PHPUnit\Framework\TestCase;

class AnchorTest extends TestCase
{
    public function positionsProvider()
    {
        return [
            [Anchor::CENTER, Anchor::CENTER, -5, -5],
            [Anchor::LEFT, Anchor::CENTER, 0, -5],
            [Anchor::RIGHT, Anchor::BOTTOM, -10, -10],
            [Anchor::LEFT, Anchor::TOP, 0, 0],
        ];
    }

    /**
     * @test
     * @dataProvider positionsProvider
     */
    public function changeBoundedRect($anchorX, $anchorY, $x, $y)
    {
        $anchor = new Anchor($anchorX, $anchorY);
        $this->assertEquals(new \SDL_Rect($x, $y, 10, 10), $anchor->getBoundedRect(0, 0, 10, 10));
    }
}
