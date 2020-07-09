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
    public function keyPressedTest()
    {
        $keyboard = $this->createMock(Keyboard::class);
        $keyboard->method('getKey')->willReturnOnConsecutiveCalls(false, true);
        $buttonBinding = new ButtonBinding([100]);
        $buttonAction = new ButtonAction([$buttonBinding]);
        $inputActions = new InputActions(['Move' => $buttonAction], $keyboard);

        $inputActions->update();
        $this->assertFalse($inputActions->getValueForAction('Move'));

        $inputActions->update();
        $this->assertTrue($inputActions->getValueForAction('Move'));
    }

    /** @test */
    public function keyReleasedTest()
    {
        $keyboard = $this->createMock(Keyboard::class);
        $keyboard->method('getKey')->willReturnOnConsecutiveCalls(true, false);
        $buttonBinding = new ButtonBinding([100]);
        $buttonAction = new ButtonAction([$buttonBinding]);
        $inputActions = new InputActions(['Move' => $buttonAction], $keyboard);

        $inputActions->update();
        $this->assertTrue($inputActions->getValueForAction('Move'));

        $inputActions->update();
        $this->assertFalse($inputActions->getValueForAction('Move'));
    }
}
