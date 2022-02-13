<?php

declare(strict_types=1);

namespace Myriapod\Enemy;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\Sprite;
use PhpGame\TextureRepository;
use PhpGame\TimeUpdatableInterface;
use PhpGame\Vector2Float;

class FlyingEnemy implements DrawableInterface, TimeUpdatableInterface
{
    private Sprite $sprite;
    private int $movingX;
    private int $dx;
    private int $dy;
    private int $type;
    private int $health;
    private float $timer;
    private string $image;

    public function __construct(private TextureRepository $textureRepository, float $playerX)
    {
        $side = random_int(0, 1);
        if ($playerX < 160) {
            $side = 1;
        } elseif ($playerX > 320) {
            $side = 0;
        }
        $this->sprite = new Sprite($this->textureRepository['blank.png'], 550 * $side - 35, 688);
        $this->movingX = 1;
        $this->dx = 1 - 2 * $side;
        $this->dy = [-1, 1][random_int(0, 1)];
        $this->type = random_int(0, 2);
        $this->health = 1;
        $this->timer = 0;
        $this->image = 'blank.png';
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->draw($renderer);
    }

    public function update(float $deltaTime): void
    {
        $this->timer += $deltaTime;
        $x = $this->sprite->getPosition()->x + $this->dx * $this->movingX * (3 - abs($this->dy)) * $deltaTime * 60;
        $y = $this->sprite->getPosition()->y + $this->dy * (3 - abs($this->dx * $this->movingX)) * $deltaTime * 60;

        $this->sprite->setPosition(new Vector2Float($x, $y));

        if ($y < 592 || $y > 784) {
            $this->movingX = random_int(0, 1);
            $this->dy = -$this->dy;
        }
        $animFrame = [0, 2, 1, 2][abs(intdiv((int)($this->timer * 60), 4) % 4)];
        $image = 'meanie'.$this->type.$animFrame.'.png';
        if ($this->image !== $image) {
            $this->image = $image;
            $this->sprite->updateTexture($this->textureRepository[$image]);
        }
    }

    public function isAlive(): bool
    {
        return $this->health > 0;
    }

    public function outOfBound(): bool
    {
        $x = $this->sprite->getPosition()->x;

        return $x < -35 || $x > 515;
    }

    public function collideWith(\SDL_Rect $rect): bool
    {
        return $this->sprite->getBoundedRect()->HasIntersection($rect);
    }

    public function damage(int $damage): void
    {
        $this->health -= $damage;
    }

    public function getPosition(): Vector2Float
    {
        return $this->sprite->getPosition();
    }
}
