<?php

declare(strict_types=1);

namespace Bunner\Row;

use Bunner\Obstacle\Mover;
use PhpGame\SDL\Renderer;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

abstract class ActiveRow extends Row
{
    private float $timer;
    /** @var array<int, Mover>  */
    protected array $children = [];

    public function __construct(TextureRepository $textureRepository, int $index, ?Row $previous = null)
    {
        parent::__construct($textureRepository, $index, $previous);

        $this->timer = 0.0;
        $x = -self::ROW_WIDTH / 2 - 70;
        while ($x < self::ROW_WIDTH / 2 + 70) {
            $x += random_int(240, 480);
            $pos = new Vector2Float(self::ROW_WIDTH / 2 + ($this->dx > 0 ? $x : -$x), 0);
            $this->children[] = $this->createChild($pos);
        }
    }

    abstract protected function createChild(Vector2Float $position): Mover;

    public function update(float $deltaTime): void
    {
        parent::update($deltaTime);

        foreach ($this->children as $i => $child) {
            if ($child->getX() < -70 || $child->getX() > self::ROW_WIDTH + 70) {
                unset($this->children[$i]);
            }
            $child->setY($this->sprite->getPosition()->y);
            $child->update($deltaTime);
        }

        $this->timer -= $deltaTime;

        if ($this->timer < 0) {
            $pos = new Vector2Float($this->dx < 0 ? self::ROW_WIDTH + 70 : -70, 0);
            $this->children[] = $this->createChild($pos);
            $this->timer = (1 + random_int(0,1000)/1000) * (4 / abs($this->dx));
        }
    }

    public function draw(Renderer $renderer): void
    {
        parent::draw($renderer);
        foreach ($this->children as $child) {
            $child->draw($renderer);
        }
    }
}
