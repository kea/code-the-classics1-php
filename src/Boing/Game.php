<?php
declare(strict_types=1);

namespace Boing;

use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\SoundManager;

class Game implements DrawableInterface
{
    private const PLAY = 0;
    private const GOAL = 1;
    /** @var array|Bat[] */
    public array $bats;
    public Ball $ball;
    /** @var array|Impact[] */
    public array $impacts;
    public int $aiOffset;

    private int $fieldWidth;
    private int $fieldHeight;
    private ManualTimer $ballOutTimer;
    private ?SoundManager $soundManager;
    private int $scoringPlayer = 0;
    private int $status;

    public function __construct(
        int $fieldWidth,
        int $fieldHeight,
        callable $control1,
        callable $control2
    )
    {
        $this->fieldWidth = $fieldWidth;
        $this->fieldHeight = $fieldHeight;
        $this->bats = [new Bat(0, $control1), new Bat(1, $control2)];
        $this->ball = new Ball(-1, $this->fieldWidth, $this->fieldHeight, $this);
        $this->impacts = [];
        $this->aiOffset = 0;
        $this->ballOutTimer = new ManualTimer();
        $this->status = self::PLAY;
    }

    public function update(float $deltaTime): void
    {
        $updatableObjects = array_merge($this->bats, [$this->ball], $this->impacts);
        foreach ($updatableObjects as $object) {
            $object->update($deltaTime);
        }

        foreach ($this->impacts as $key => $impact) {
            if (!$impact->getAnimation()->isRunning()) {
                unset($this->impacts[$key]);
            }
        }

        $this->ballOut($deltaTime);
    }

    public function draw(Renderer $renderer): void
    {
        $name = __DIR__.'/images/table.png';
        $renderer->drawImage($name, 0, 0, $this->fieldWidth, $this->fieldHeight);
        if ($this->status === self::GOAL) {
            $name = __DIR__.'/images/effect'.(1 - $this->scoringPlayer).'.png';
            $renderer->drawImage($name, 0, 0, $this->fieldWidth, $this->fieldHeight);
        }

        $drawableObjects = array_merge($this->bats, [$this->ball], $this->impacts);
        foreach ($drawableObjects as $object) {
            $object->draw($renderer);
        }

        $this->drawScores($renderer);
    }

    private function drawScores(Renderer $renderer): void
    {
        foreach ($this->bats as $p => $bat) {
            $score = sprintf("%02d", $bat->getScore());
            for ($i = 0; $i < 2; $i++) {
                $colour = "0";
                if ($bat->getStatus() === Bat::LOOSE) {
                    $colour = $p === 0 ? "2" : "1";
                }
                $image = "digit".$colour.$score[$i];
                $name = __DIR__.'/images/'.$image.'.png';
                $renderer->drawImage($name, 255 + (160 * $p) + ($i * 55), 46, 75, 75);
            }
        }
    }

    public function playSound(string $name, int $count = 1): void
    {
        if ($this->soundManager === null) {
            return;
        }

        if ($this->soundManager->getMusicVolume() === 0) {
            return;
        }

        $name .= random_int(0, $count - 1);

        $this->soundManager->playSound($name.'.ogg');
    }

    private function ballOut(float $deltaTime): void
    {
        if ($this->status === self::GOAL) {
            $this->ballOutTimer->decreaseTime($deltaTime);

            return;
        }

        if (!$this->ball->isOut()) {
            return;
        }

        $this->status = self::GOAL;
        $this->scoringPlayer = $this->ball->x() <= 0 ? 1 : 0;

        $this->bats[$this->scoringPlayer]->scored();
        $this->bats[1 - $this->scoringPlayer]->loose();
        $this->playSound("score_goal", 1);

        $newBall = function() {
            $this->bats[1 - $this->scoringPlayer]->play();
            $direction = $this->scoringPlayer === 0 ? 1 : -1;
            $this->ball = new Ball($direction, $this->fieldWidth, $this->fieldHeight, $this);
            $this->status = self::PLAY;
        };

        $this->ballOutTimer->start(0.33, $newBall);
    }

    /**
     * @param SoundManager $soundManager
     */
    public function setSoundManager(SoundManager $soundManager): void
    {
        $this->soundManager = $soundManager;
    }
}
