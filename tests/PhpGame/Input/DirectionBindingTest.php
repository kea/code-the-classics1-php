<?php

namespace PhpGame\Input;

use PhpGame\Vector2Float;
use PHPUnit\Framework\TestCase;

/**
 * Class DirectionBindingTest
 * @package PhpGame\Input
 * @covers \PhpGame\Input\DirectionBinding
 */
class DirectionBindingTest extends TestCase
{
    /**
     * @var DirectionBinding
     */
    private DirectionBinding $buttonBinding;

    public function setUp(): void
    {
        parent::setUp();
        $this->buttonBinding = new DirectionBinding(
            [
                DirectionBinding::Up => 100,
                DirectionBinding::Down => 101,
                DirectionBinding::Left => 102,
                DirectionBinding::Right => 103,
            ]
        );
    }

    public function provideDefaultMoveVectors(): array
    {
        return [
            [new Vector2Float(0, 0)],
            [new Vector2Float(-1, 0)],
            [new Vector2Float(1, 1)],
        ];
    }

    /**
     * @test
     * @dataProvider provideDefaultMoveVectors
     */
    public function returnsDefault($defaultDirection)
    {
        $keyboard = $this->createMock(Keyboard::class);
        $keyboard->method('getKey')->willReturn(false);

        $this->assertEquals(
            $defaultDirection,
            $this->buttonBinding->updateByKeyboard($keyboard, $defaultDirection)
        );
    }

    /**
     * @test
     * @dataProvider provideKeysAndMoveVectors
     * @param array        $keyMap
     * @param Vector2Float $expectedMove
     */
    public function returnsKeyPressedAsVector(array $keyMap, Vector2Float $expectedMove)
    {
        $keyboard = $this->createMock(Keyboard::class);
        $keyboard->method('getKey')->willReturnMap($keyMap);

        $defaultMove = new Vector2Float(0, 0);
        $actualMove = $this->buttonBinding->updateByKeyboard($keyboard, $defaultMove);
        $this->assertEquals($expectedMove, $actualMove);
    }

    public function provideKeysAndMoveVectors()
    {
        return [
            [[[100, true], [101, false], [102, false], [103, false]], new Vector2Float(0, -1)],
            [[[100, false], [101, true], [102, false], [103, true]], new Vector2Float(1, 1)],
            [[[100, true], [101, true], [102, true], [103, false]], new Vector2Float(-1, 0)],
        ];
    }
}
