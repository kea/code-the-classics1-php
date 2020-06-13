<?php

namespace PhpGame\Input;

use PHPUnit\Framework\TestCase;

/**
 * Class InputActionsTest
 * @package PhpGame\Input
 * @covers \PhpGame\Input\InputActions
 */
class InputActionsTest extends TestCase
{
    /** @test */
    public function keypressedTest()
    {
        $buttonBinding = new ButtonBinding([100]);
        $buttonAction = new ButtonAction([$buttonBinding]);
        $inputActions = new InputActions(['Move' => $buttonAction]);

        $this->assertFalse($inputActions->getValueForAction('Move'));
        $keyboard = $this->createMock(Keyboard::class);
        $keyboard->method('getKey')->willReturn(true);

        $inputActions->updateByKeyboard($keyboard);
        $this->assertTrue($inputActions->getValueForAction('Move'));
    }

    /** @test */
    public function releasedTest()
    {
        $buttonBinding = new ButtonBinding([100]);
        $buttonAction = new ButtonAction([$buttonBinding]);
        $inputActions = new InputActions(['Move' => $buttonAction]);

        $keyboard = $this->createMock(Keyboard::class);
        $keyboard->method('getKey')->willReturn(true);
        $inputActions->updateByKeyboard($keyboard);

        $this->assertTrue($inputActions->getValueForAction('Move'));

        $keyboard = $this->createMock(Keyboard::class);
        $keyboard->method('getKey')->willReturn(false);
        $inputActions->updateByKeyboard($keyboard);

        $this->assertFalse($inputActions->getValueForAction('Move'));

    }
}
