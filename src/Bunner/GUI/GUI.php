<?php

namespace Bunner\GUI;

use Bunner\Game;
use Bunner\Score;
use PhpGame\Animation;
use PhpGame\Camera;
use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\Sprite;
use PhpGame\TextSprite;
use PhpGame\TextureRepository;
use PhpGame\TimeUpdatableInterface;
use PhpGame\Vector2Float;

class GUI implements DrawableInterface, TimeUpdatableInterface
{
    private const PLAY = 'PLAY';
    private const MENU = 'MENU';
    private const GAME_OVER = 'GAME_OVER';
    private float $timeElapsed = 0;
    private int $fps =0;
    private int $tick = 0;
    private TextSprite $scoreText;
    private TextSprite $maxScoreText;
    private Camera $camera;
    private TextureRepository $textureRepository;
    private string $state = self::MENU;
    /** @var array<string, Sprite>  */
    private array $sprites = [];
    private Animation $menuAnimation;
    private \SDL_Rect $menuPosition;
    private Score $score;

    public function __construct(TextureRepository $textureRepository, Camera $camera, Score $score)
    {
        $this->camera = $camera;
        $this->textureRepository = $textureRepository;
        $this->score = $score;
    }

    public function init(): void
    {
        $this->scoreText = new TextSprite($this->textureRepository["digits.png"], '0', new Vector2Float(360.0, 20.0));
        $this->maxScoreText = new TextSprite($this->textureRepository["digits.png"], '0', new Vector2Float(20.0, 20.0));
        $this->sprites["title"] = new Sprite($this->textureRepository["title.png"], Game::WIDTH / 2, Game::HEIGHT / 2);
        $this->sprites["gameover"] = new Sprite($this->textureRepository["gameover.png"], Game::WIDTH / 2, Game::HEIGHT / 2);

        $images = [
            $this->textureRepository['start0.png'],
            $this->textureRepository['start1.png'],
            $this->textureRepository['start2.png'],
            $this->textureRepository['start1.png'],
        ];
        $this->menuAnimation = new Animation($images, 10, true);
        $this->menuAnimation->startAnimation();
        $this->menuPosition = new \SDL_Rect(
            (Game::WIDTH - 270) / 2,
            Game::HEIGHT - 240,
            $this->textureRepository['start0.png']->getWidth(),
            $this->textureRepository['start0.png']->getHeight()
        );
    }

    public function update(float $deltaTime): void
    {
        $this->menuAnimation->update($deltaTime);

        ++$this->tick;
        $this->timeElapsed += $deltaTime;
        if ($this->timeElapsed < 1.0) {
            return;
        }

        $this->fps = $this->tick;
        $this->timeElapsed -= 1.0;
        $this->tick = 0;
    }

    public function draw(Renderer $renderer): void
    {
        $this->scoreText->updateText((string)($this->score->getScore()));
        $this->maxScoreText->updateText((string)($this->score->getMaxScore()));
        $camera = $renderer->getCamera();
        $renderer->setCamera($this->camera);
        $this->scoreText->draw($renderer);
        $this->maxScoreText->draw($renderer);

        if ($this->state === self::MENU) {
            $this->sprites['title']->draw($renderer);
            $renderer->drawTexture($this->menuAnimation->getCurrentFrame(), $this->menuPosition);
        }
        if ($this->state === self::GAME_OVER) {
            $this->sprites['gameover']->draw($renderer);
        }

        $renderer->setCamera($camera);
    }

    public function changeStateToPlay(): void
    {
        $this->state = self::PLAY;
    }

    public function changeStateToGameOver(): void
    {
        $this->state = self::GAME_OVER;
    }

    public function changeStateToMenu(): void
    {
        $this->state = self::MENU;
    }
}