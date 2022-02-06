<?php

declare(strict_types=1);

namespace Myriapod;

use Myriapod\Bullet\Bullet;
use Myriapod\Bullet\Bullets;
use Myriapod\GUI\GUI;
use Myriapod\Player\Pod;
use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;
use PhpGame\SoundManager;
use PhpGame\TextureRepository;
use PhpGame\TimeUpdatableInterface;

class Game implements DrawableInterface, TimeUpdatableInterface, SoundEmitterInterface
{
    public const HEIGHT = 800;
    public const WIDTH = 480;

    use SoundEmitterTrait;

    private ?Pod $player = null;
    private int $wave = -1;
    private int $time = 0;
    private Bullets $bullets;
    private Grid $grid;

    public function __construct(
        private TextureRepository $textureRepository,
        SoundManager $soundManager,
        private InputActions $inputActions,
        private GUI $gui,
        EntityRegistry $entityRegistry, // tb removed?
        private Score $score
    ) {
        $this->entityRegistry = $entityRegistry;
        $this->bullets = new Bullets();
        $this->start();
        $this->soundManager = $soundManager;
    }

    public function update(float $deltaTime): void
    {
        $updatableObjects = $this->getUpdatableObjects();
        foreach ($updatableObjects as $object) {
            $object->update($deltaTime);
            if ($object instanceof Bullet && $object->getPosition()->y < 0) {
                $this->bullets->remove($object);
            }
        }
        $this->gui->update($deltaTime);
    }

    public function draw(Renderer $renderer): void
    {
        $renderer->drawTexture(
            $this->textureRepository["bg".(max($this->wave, 0) % 3).'.png'],
            new \SDL_Rect(0, 0,self::WIDTH, self::HEIGHT)
        );

        $objectToDraw = $this->getUpdatableObjects();

        $sortScore = fn($obj) => (($obj instanceof Explosion) ? 10000 : 0) + $obj->getPosition()->y;
        $sort = fn($a, $b) => $sortScore($a) <=> $sortScore($b);

        usort($objectToDraw, $sort);

        # Draw the flying enemy on top of everything else
        //all_objs.append(self.flying_enemy)

        foreach ($objectToDraw as $object) {
            $object->draw($renderer);
        }

        $this->gui->draw($renderer);
    }

    public function start(): void
    {
        $this->score->reset();
        $this->wave = -1;
        $this->time = 0;

        $this->grid = new Grid($this->textureRepository, $this->wave);
        $this->grid->addRock(10, 10);

        $this->bullets->reset();
        $this->explosions = [];
        $this->segments = [];
        $this->flying_enemy = null;
    }

    public function isGameOver(): bool
    {
        if ($this->player === null) {
            return false;
        }

        return !$this->player->isAlive() && !$this->player->isAnimationPlaying();
    }

    public function addPlayer(): void
    {
        $this->player = new Pod($this->textureRepository, $this->inputActions, $this->bullets);
        if ($this->soundManager) {
            $this->player->setSoundManager($this->soundManager);
        }
        $this->entityRegistry->add($this->player);
    }

    protected function getUpdatableObjects(): array
    {
        //all_objs = sum(self.grid, self.bullets + self.segments + self.explosions + [self.player])
        $objectToDraw = [];
        foreach ($this->bullets as $bullet) {
            $objectToDraw[] = $bullet;
        }
        foreach ($this->grid as $obj) {
            $objectToDraw[] = $obj;
        }
        if ($this->player) {
            $objectToDraw[] = $this->player;
        }

        return $objectToDraw;
    }
}
