<?php

namespace Cavern;

use PhpGame\Anchor;
use PhpGame\SDL\Texture;
use PhpGame\Sprite;
use PhpGame\Vector2Float;
use PHPUnit\Framework\TestCase;

class ColliderActorTest extends TestCase
{
    /**
     * @covers \Cavern\ColliderActor
     * @covers \PhpGame\Anchor
     */
    public function testBoundaries()
    {
        $texture = $this->createStub(Texture::class);
        $texture->method('getWidth')->willReturn(8);
        $texture->method('getHeight')->willReturn(6);
        $actor = new ColliderActor(new Sprite($texture, 10.0, 10.0));

        self::assertSame(6, $actor->getCollider()->x);
        self::assertSame(8, $actor->getCollider()->w);
        self::assertSame(7, $actor->getCollider()->y);
        self::assertSame(6, $actor->getCollider()->h);
    }

    /**
     * @covers \Cavern\ColliderActor
     * @covers \PhpGame\Anchor
     */
    public function testBoundariesAnchorLeftBottom()
    {
        $texture = $this->createStub(Texture::class);
        $texture->method('getWidth')->willReturn(8);
        $texture->method('getHeight')->willReturn(6);
        $actor = new ColliderActor(new Sprite($texture, 10.0, 10.0, Anchor::LeftBottom()));

        self::assertSame(10, $actor->getCollider()->x);
        self::assertSame(8, $actor->getCollider()->w);
        self::assertSame(4, $actor->getCollider()->y);
        self::assertSame(6, $actor->getCollider()->h);
    }
}
