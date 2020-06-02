<?php
declare(strict_types=1);

namespace Boing;

use PhpGame\SDL\Screen;
use PhpGame\SoundManager;

class Game implements DrawableInterface
{
    /** @var array|Bat[] */
    public array $bats;
    public Ball $ball;
    /** @var array|Impact[] */
    public array $impacts;
    public int $aiOffset;

    private int $fieldWidth;
    private int $fieldHeight;
    private ManualTimer $ballOutTimer;
    private SoundManager $soundManager;
    private int $scoringPlayer;

    public function __construct(
        int $fieldWidth,
        int $fieldHeight,
        callable $control1,
        callable $control2
    )
    {
        $this->fieldWidth = $fieldWidth;
        $this->fieldHeight = $fieldHeight;
        $this->bats = [new Bat(0, $control1, $this), new Bat(1, $control2, $this)];
        $this->ball = new Ball(-1, $this->fieldWidth, $this->fieldHeight, $this);
        $this->impacts = [];
        $this->aiOffset = 0;
        $this->ballOutTimer = new ManualTimer();
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

    public function draw(Screen $screen)
    {
        $name = __DIR__.'/images/table.png';
        $screen->drawImage($name, 0, 0, $this->fieldWidth, $this->fieldHeight);

        if ($this->ballOutTimer->isStarted()) {
            $name = __DIR__.'/images/effect'.(1 - $this->scoringPlayer).'.png';
            $screen->drawImage($name, 0, 0, $this->fieldWidth, $this->fieldHeight);
        }

        $drawableObjects = array_merge($this->bats, [$this->ball], $this->impacts);
        foreach ($drawableObjects as $object) {
            $object->draw($screen);
        }

        $this->drawScores($screen);
    }

    private function drawScores(Screen $screen): void
    {
        for ($p = 0; $p < 2; $p++) {
            $score = sprintf("%02d", $this->bats[$p]->score());
            for ($i = 0; $i < 2; $i++) {
                $colour = "0";
                if ($this->ballOutTimer->isStarted()) {
                    $colour = $p === 0 ? "2" : "1";
                }
                $image = "digit".$colour.$score[$i];
                $name = __DIR__.'/images/'.$image.'.png';
                $screen->drawImage($name, 255 + (160 * $p) + ($i * 55), 46, 75, 75);
            }
        }
    }

    public function playSound($name, $count = 1)
    {
        if ($this->soundManager === null) {
            return;
        }

        if ($this->soundManager->getVolume() === 0) {
            return;
        }

        $name .= random_int(0, $count - 1);

        $this->soundManager->play($name.'.ogg');
    }

    private function ballOut(float $deltaTime): void
    {
        if (!$this->ball->out()) {
            return;
        }

        if ($this->ballOutTimer->isStarted()) {
            $this->ballOutTimer->decreaseTime($deltaTime);
            return;
        }

        $this->scoringPlayer = $this->ball->x() < $this->fieldWidth ? 1 : 0;

        $this->bats[$this->scoringPlayer]->incScore();
        $this->playSound("score_goal", 1);

        $newBall = function() {
            $direction = $this->scoringPlayer === 0 ? 1 : -1;
            $this->ball = new Ball($direction, $this->fieldWidth, $this->fieldHeight, $this);
        };

        $this->ballOutTimer->start(3, $newBall);
    }

    public function getScoringPlayer(): int
    {
        return $this->scoringPlayer;
    }

    /**
     * @param SoundManager $soundManager
     */
    public function setSoundManager(SoundManager $soundManager): void
    {
        $this->soundManager = $soundManager;
    }
}
