<?php

namespace Myriapod\Player;

use Myriapod\Bullet\Bullet;
use Myriapod\Bullet\Bullets;
use Myriapod\Enemy\Segments;
use Myriapod\Game;
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
    use SoundEmitterTrait;
    private const RELOAD_TIME = 0.166;
    private const RESPAWN_TIME = 1.666;
    private const INVULNERABILITY_TIME = 1.666;

    private Sprite $sprite;
    private int $lastDirectionFrame = 0;
    private int $lastFireFrame = 0;
    private float $fireTimer = 0;
    private float $timer = 0;
    private bool $alive = true;
    private int $lives = 3;

    public function __construct(
        private TextureRepository $textureRepository,
        private InputActions $inputActions,
        private Bullets $bullets
    )
    {
        $this->sprite = new Sprite($textureRepository['player00.png'], 240, 768);
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->draw($renderer);
    }

    public function update(float $deltaTime): void
    {
        $this->timer += $deltaTime;
        if ($this->isAlive()) {
            /** @var Vector2Float $direction */
            $direction = $this->inputActions->getValueForAction('Move');

            if (!$direction->isZero()) {
                // get in account diagonal moves
                //self.move(dx, 0, 3 - abs(dy))
                //self.move(0, dy, 3 - abs(dx))
                $position = $this->sprite->getPosition()->add($direction->multiplyFloat($deltaTime * 180));
                $this->boundToAllowedPositions($position);
                $this->sprite->setPosition($position);

                $frame = $this->getSpriteFrame($direction);
                $this->lastDirectionFrame = $frame;
            }

            $this->updateFire($deltaTime, $this->inputActions->getValueForAction('Fire'));
        } elseif ($this->timer > self::RESPAWN_TIME) {
            $this->alive = true;
            $this->timer = 0;
            $this->sprite->setPosition(new Vector2Float(240.0, 768.0));
            //game.clear_rocks_for_respawn(*self.pos)
        }

        $image = "blank.png";
        $invulnerable = $this->timer <= self::INVULNERABILITY_TIME;
        if ($this->alive && (!$invulnerable || $this->invulnerableFrame() === 0)) {
            $image = 'player'.$this->lastDirectionFrame.$this->lastFireFrame.'.png';
        }
        $this->sprite->updateTexture($this->textureRepository[$image]);
    }

    public function checkCollision(Segments $enemies): void
    {
        foreach ($enemies as $enemy) {
            if ($enemy->collideWith($this->sprite->getBoundedRect()) && ($this->timer > self::INVULNERABILITY_TIME)) {
                $this->playSound("player_explode0.ogg");
                //game.explosions.append(Explosion(self.pos, 1))
                $this->alive = false;
                $this->timer = 0;
                --$this->lives;
            }
        }
    }

    private function invulnerableFrame(): int
    {
        $frame = floor($this->timer * 60);

        return $frame % 2;
    }

    private function updateFire(float $deltaTime, bool $fire): void
    {
        $this->fireTimer -= $deltaTime;
        if ($this->fireTimer < 0 && ($this->lastFireFrame > 0 || $fire)) {
            if ($this->lastFireFrame === 0) {
                $this->playSound("laser0.ogg");
                $laserPos = (clone $this->sprite->getPosition())->sub(new Vector2Float(.0, -8.0));
                $this->bullets->append(new Bullet($this->textureRepository, $laserPos));
            }
            $this->lastFireFrame = ($this->lastFireFrame + 1) % 3;
            $this->fireTimer = self::RELOAD_TIME;
        }
    }

    public function isAlive(): bool
    {
        return $this->alive;
    }

    public function getLives(): int
    {
        return $this->lives;
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

    public function getPosition(): Vector2Float
    {
        return $this->sprite->getPosition();
    }

    /**
     * @param Vector2Float $position
     * @return void
     */
    private function boundToAllowedPositions(Vector2Float $position): void
    {
        if ($position->x < 40) {
            $position->x = 40;
        }
        if ($position->x > Game::WIDTH - 40) {
            $position->x = Game::WIDTH - 40;
        }
        if ($position->y < 592) {
            $position->y = 592;
        }
        if ($position->y > Game::HEIGHT - 16) {
            $position->y = Game::HEIGHT - 16;
        }
    }
}
