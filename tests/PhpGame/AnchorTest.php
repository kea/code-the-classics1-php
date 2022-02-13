<?php

namespace PhpGame;

use PHPUnit\Framework\TestCase;

/**
 * @covers Anchor
 */
class AnchorTest extends TestCase
{
    public function positionsProvider()
    {
        return [
            [Anchor::CenterCenter(), -5, -5],
            [Anchor::LeftCenter(), 0, -5],
            [Anchor::RightBottom(), -10, -10],
            [Anchor::LeftTop(), 0, 0],
            [new Anchor(0.2, 0.4), -2, -4],
        ];
    }

    /**
     * @test
     * @dataProvider positionsProvider
     */
    public function changeBoundedRect(Anchor $anchor, $x, $y)
    {
        $this->assertEquals(new \SDL_Rect($x, $y, 10, 10), $anchor->getBoundedRect(0, 0, 10, 10));
    }
}
