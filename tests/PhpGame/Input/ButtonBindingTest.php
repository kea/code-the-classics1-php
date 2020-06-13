<?php

namespace PhpGame\Input;

use PHPUnit\Framework\TestCase;

/**
 * Class ButtonBindingTest
 * @package PhpGame\Input
 * @covers \PhpGame\Input\ButtonBinding
 */
class ButtonBindingTest extends TestCase
{
    /** @test */
    public function returnsDefault()
    {
        $keyboard = $this->createMock(Keyboard::class);
        $keyboard->method('getKey')->willReturn(false);

        $buttonBinding = new ButtonBinding([100]);
        $this->assertFalse($buttonBinding->updateByKeyboard($keyboard, false));

        $keyboard = $this->createMock(Keyboard::class);
        $keyboard->method('getKey')->willReturn(true);

        $buttonBinding = new ButtonBinding([100]);
        $this->assertTrue($buttonBinding->updateByKeyboard($keyboard, true));
    }

    /** @test */
    public function returnsKeyPressed()
    {
        $keyboard = $this->createMock(Keyboard::class);
        $keyboard->method('getKey')->willReturn(true);

        $buttonBinding = new ButtonBinding([100]);
        $this->assertTrue($buttonBinding->updateByKeyboard($keyboard, false));
    }
}
