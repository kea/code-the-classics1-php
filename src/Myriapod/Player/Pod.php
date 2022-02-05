<?php

namespace Myriapod\Player;

use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;
use PhpGame\Sprite;
use PhpGame\TextureRepository;
use PhpGame\TimeUpdatableInterface;
use PhpGame\Vector2Float;

class Pod implements DrawableInterface, TimeUpdatableInterface, SoundEmitterInterface
{
    private const RELOAD_TIME = 0.166;
    use SoundEmitterTrait;
    private Sprite $sprite;
    private int $lastDirectionFrame = 0;
    private int $lastFireFrame = 0;
    private float $fireTimer = 0;

    public function __construct(private TextureRepository $textureRepository, private InputActions $inputActions)
    {
        $this->sprite = new Sprite($textureRepository['player00.png'], 240, 768);
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->draw($renderer);
    }

    public function update(float $deltaTime): void
    {
        /** @var Vector2Float $direction */
        $direction = $this->inputActions->getValueForAction('Move');

        if (!$direction->isZero()) {
            $position = $this->sprite->getPosition()->add($direction->multiplyFloat($deltaTime * 180));
            $this->sprite->setPosition($position);

            $frame = $this->getSpriteFrame($direction);
            $this->lastDirectionFrame = $frame;
        }

        $this->updateFire($deltaTime, $this->inputActions->getValueForAction('Fire'));

        $this->sprite->updateTexture($this->textureRepository['player'.$this->lastDirectionFrame.$this->lastFireFrame.'.png']);
    }

    private function updateFire(float $deltaTime, bool $fire): void
    {
        $this->fireTimer -= $deltaTime;
        if ($this->fireTimer < 0 && ($this->lastFireFrame > 0 || $fire)) {
            if ($this->lastFireFrame === 0) {
                $this->playSound("laser");
                //    game.bullets.append(Bullet((self.x, self.y - 8)))
            }
            $this->lastFireFrame = ($this->lastFireFrame + 1) % 3;
            $this->fireTimer = self::RELOAD_TIME;
        }
    }

    public function isAlive(): bool
    {
        return true;
    }

    public function isAnimationPlaying(): bool
    {
        return false;
    }

    /**
     * @param Vector2Float $direction
     * @return int
     */
    protected function getSpriteFrame(Vector2Float $direction): int
    {
        if ($direction->x === .0) {
            $frame = 0;
        } elseif ($direction->y === .0) {
            $frame = 2;
        } elseif (abs($direction->x + $direction->y) === 1.0) {
            $frame = 1;
        } else {
            $frame = 3;
        }

        return $frame;
    }
}
