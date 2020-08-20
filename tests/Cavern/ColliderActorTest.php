<?php

namespace Cavern;

use PhpGame\Vector2Float;
use PHPUnit\Framework\TestCase;

class ColliderActorTest extends TestCase
{
    public function testBoundaries()
    {
        $actor = new ColliderActor(new Vector2Float(10.0, 10.0), 6, 8);

        self::assertSame(6.0, $actor->top());
        self::assertSame(14.0, $actor->bottom());
    }
}
