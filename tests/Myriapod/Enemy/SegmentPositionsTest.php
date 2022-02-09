<?php

declare(strict_types=1);

namespace Myriapod\Enemy;

use PHPUnit\Framework\TestCase;

class SegmentPositionsTest extends TestCase
{
    /**
     * @covers \Myriapod\Enemy\SegmentPositions
     */
    public function testIsOccupied()
    {
        $segmentPositions = new SegmentPositions();
        $this->assertFalse($segmentPositions->isOccupied(0, 1));
        $this->assertFalse($segmentPositions->isOccupied(1, 1));

        $segmentPositions->occupy(0, 1);
        $this->assertTrue($segmentPositions->isOccupied(0, 1));
        $this->assertFalse($segmentPositions->isOccupied(1, 1));

        $segmentPositions->reset();
        $this->assertFalse($segmentPositions->isOccupied(0, 1));
        $this->assertFalse($segmentPositions->isOccupied(1, 1));
    }
}
