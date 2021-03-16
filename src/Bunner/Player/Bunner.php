<?php

namespace Bunner\Player;

use Bunner\Game;
use Bunner\Row\Row;
use Bunner\Row\RowsCollection;
use PhpGame\DrawableInterface;
use PhpGame\Input\InputActions;
use PhpGame\SDL\Renderer;
use PhpGame\SoundEmitterInterface;
use PhpGame\SoundEmitterTrait;
use PhpGame\Sprite;
use PhpGame\TextureRepository;
use PhpGame\Vector2Float;

class Bunner implements SoundEmitterInterface, DrawableInterface
{
    use SoundEmitterTrait;

    private const MOVE_DISTANCE = 40;
    private Sprite $sprite;
    private string $state;
    private Vector2Float $direction;
    private float $timer;
    private array $inputQueue = [];
    private TextureRepository $textureRepository;
    private InputActions $inputActions;
    private Vector2Float $lastInput;
    private float $minY;
    private string $image = 'blank.png';
    private RowsCollection $rows;

    /**
     * Bunner constructor.
     * @param TextureRepository $textureRepository
     * @param InputActions      $inputActions
     * @param RowsCollection    $rows
     */
    public function __construct(TextureRepository $textureRepository, InputActions $inputActions, RowsCollection $rows)
    {
        $this->sprite = new Sprite($textureRepository[$this->image], Game::WIDTH/2, Game::HEIGHT - 320);
        $this->state = PlayerState::ALIVE;
        $this->direction = new Vector2Float(0, 1);
        $this->timer = 0;
        $this->minY = $this->getSprite()->getPosition()->y;
        $this->textureRepository = $textureRepository;
        $this->inputActions = $inputActions;
        $this->rows = $rows;
    }

    public function handleInput(): void
    {
        /** @var Vector2Float $direction */
        $direction = $this->inputActions->getValueForAction('Move');
        if ($direction->isZero()) {
            $this->lastInput = $direction;
        } elseif (!$direction->isEqual($this->lastInput)) {
            $this->inputQueue[] = $direction;
            $this->lastInput = $direction;
        }

        if ($this->timer > 0 || count($this->inputQueue) === 0) {
            return;
        }

        $direction = array_shift($this->inputQueue);
        $jumpPath = (clone $direction)->multiplyFloat(self::MOVE_DISTANCE);
        $nextPosition = (clone $this->sprite->getPosition())->add($jumpPath);

        foreach ($this->rows as $row) {
            if (!$row->contains($nextPosition)) {
                continue;
            }
            if ($row->allowMovement($nextPosition->x)) {
                $this->direction = $direction;
                $this->timer = 10 / 60;
                $this->playSound("jump1.wav");

                return;
            }
        }
    }

    public function getY(): float
    {
        return $this->sprite->getPosition()->y;
    }

    public function getX(): float
    {
        return $this->sprite->getPosition()->x;
    }

    public function getSprite(): Sprite
    {
        return $this->sprite;
    }

    public function update(float $deltaTime): void
    {
        if ($this->state === PlayerState::ALIVE) {
            $this->handleInput();
            $land = false;
            if ($this->timer > 0) {
                $movementDeltaTime = $this->timer < $deltaTime ? $this->timer : $deltaTime;
                $movement = (clone $this->direction)->multiplyFloat($movementDeltaTime * self::MOVE_DISTANCE * 6);
                $position = (clone $this->sprite->getPosition())->add($movement);
                $this->sprite->setPosition($position);
                $this->timer -= $movementDeltaTime;
                $land = $this->timer <= 0;
            }

            $currentRow = null;
            foreach ($this->rows as $row) {
                /** @var $row Row */
                if ($row->contains($this->sprite->getPosition())) {
                    $currentRow = $row;
                    break;
                }
            }
            if ($currentRow !== null) {
                $this->state = $currentRow->checkCollision($this);
                if ($this->state === PlayerState::ALIVE) {
                    $position = $this->sprite->getPosition();
                    $position->add($currentRow->push()->multiplyFloat($deltaTime));
                    $this->sprite->setPosition($position);

                    if ($land) {
                        $currentRow->playLandedSound();
                    }
                } else {
                    if ($this->state === PlayerState::SPLAT) {
                        $texture = $this->textureRepository["splat".$this->getDirectionFrameNumber()];
                        /** @todo splat */
                        //$currentRow->addChild(new Sprite($texture, $this->getX(), $this->getY()));
                    }
                    $this->timer = 100 / 60;
                }
            } elseif ($this->getY() > Game::HEIGHT + 80) {
                /** @todo Eagle */
                // $eagle = new Eagle($this->getX());
                $this->state = PlayerState::EAGLE;
                $this->timer = 150 / 60;
                $this->playSound("eagle0.wav");
            }
            if ($this->getX() < 16 || $this->getX() > Game::WIDTH - 16) {
                $position = $this->sprite->getPosition();
                $position = new Vector2Float(max(16, min(Game::WIDTH - 16, $this->getX())), $position->y);
                $this->sprite->setPosition($position);
            }
        } else {
            $this->timer -= $deltaTime;
        }
        $this->minY = min($this->minY, $this->getY());

        $this->image = "blank.png";
        if ($this->state === PlayerState::ALIVE) {
            $directionFrame = $this->getDirectionFrameNumber();
            $this->image = $this->timer > 0 ? "jump" : "sit";
            $this->image.= $directionFrame.".png";
        } elseif ($this->state === PlayerState::SPLASH && $this->timer > 1.4) {
            $this->image = "splash".((int)((1.66 - $this->timer) / 2)).".png";
        }
    }

    public function draw(Renderer $renderer): void
    {
        $this->sprite->updateTexture($this->textureRepository[$this->image]);
        $renderer->drawRectangle($this->sprite->getBoundedRect());
        $this->sprite->render($renderer);
    }

    private function getDirectionFrameNumber(): string
    {
        if ($this->direction->y > 0) {
            return '2';
        }
        if ($this->direction->y < 0) {
            return '0';
        }
        if ($this->direction->x > 0) {
            return '1';
        }

        return '3';
    }

    public function isAlive(): bool
    {
        return $this->state === PlayerState::ALIVE;
    }
}
