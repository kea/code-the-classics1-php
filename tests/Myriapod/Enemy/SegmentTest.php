<?php

declare(strict_types=1);

namespace Myriapod\Enemy;

use PhpGame\SDL\Texture;
use PhpGame\SoundManager;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Myriapod\Enemy\Segment
 */
class SegmentTest extends TestCase
{
    public function testMovePriorityHorizontal(): void
    {
        $soundManager = $this->createStub(SoundManager::class);
        $textureRepo = $this->createStub(TextureRepository::class);
        $textureRepo->method('offsetGet')->willReturn($this->createStub(Texture::class));
        $segment = new Segment(
            $textureRepo, 10, 10, 1, false, true, new SegmentPositions(), new Rocks($textureRepo, 1, $soundManager)
        );
        $this->assertSame(10010, $segment->movePriority(Segment::DIRECTION_UP));
        $this->assertSame(1, $segment->movePriority(Segment::DIRECTION_RIGHT));
        $this->assertSame(10, $segment->movePriority(Segment::DIRECTION_DOWN));
        $this->assertSame(100000, $segment->movePriority(Segment::DIRECTION_LEFT));
    }

    public function testMovePriorityOutOfGrid(): void
    {
        $soundManager = $this->createStub(SoundManager::class);
        $textureRepo = $this->createStub(TextureRepository::class);
        $textureRepo->method('offsetGet')->willReturn($this->createStub(Texture::class));
        $segment = new Segment(
            $textureRepo, 12, 10, 1, false, true, new SegmentPositions(), new Rocks($textureRepo, 1, $soundManager)
        );
        $phaseDuration = 1 / 60;
        $segment->update(0.001);
        $this->assertSame(10010, $segment->movePriority(Segment::DIRECTION_UP));
        $this->assertSame(1000001, $segment->movePriority(Segment::DIRECTION_RIGHT));
        $this->assertSame(10, $segment->movePriority(Segment::DIRECTION_DOWN));
        $this->assertSame(100000, $segment->movePriority(Segment::DIRECTION_LEFT));
        $segment->update($phaseDuration + 0.001);
        $this->assertSame(10010, $segment->movePriority(Segment::DIRECTION_UP));
        $this->assertSame(1000001, $segment->movePriority(Segment::DIRECTION_RIGHT));
        $this->assertSame(10, $segment->movePriority(Segment::DIRECTION_DOWN));
        $this->assertSame(100000, $segment->movePriority(Segment::DIRECTION_LEFT));
    }

    public function testUpdate()
    {
        $soundManager = $this->createStub(SoundManager::class);
        $textureRepo = $this->createStub(TextureRepository::class);
        $textureRepo->method('offsetGet')->willReturn($this->createStub(Texture::class));
        $currentCellX = 10;
        $currentCellY = 12;
        $segment = new Segment(
            $textureRepo, $currentCellX, $currentCellY, 1, false, true, new SegmentPositions(), new Rocks($textureRepo, 1, $soundManager)
        );
        $nextCellX = $currentCellX + 1;
        $nextCellY = $currentCellY;
        $x = $nextCellX * 32 + 16;
        $y = $nextCellY * 32 + 16;
        $phaseDuration = 1 / 60;
        $segment->update($phaseDuration - 0.001);
        $this->assertEquals(new Vector2Float($x, $y), $segment->getPosition());
        $segment->update($phaseDuration - 0.001);
        $this->assertEquals(new Vector2Float($x + 2, $y), $segment->getPosition());
        $segment->update($phaseDuration - 0.001);
        $this->assertEquals(new Vector2Float($x + 4, $y), $segment->getPosition());
        $segment->update($phaseDuration - 0.001);
        $this->assertEquals(new Vector2Float($x + 6, $y), $segment->getPosition());
    }
}
