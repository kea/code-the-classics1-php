<?php

namespace Myriapod\GUI;

use Myriapod\Game;
use Myriapod\Player\Pod;
use Myriapod\Score;
use PhpGame\Anchor;
use PhpGame\Animation;
use PhpGame\Camera;
use PhpGame\DigitsSprites;
use PhpGame\DrawableInterface;
use PhpGame\SDL\Renderer;
use PhpGame\Sprite;
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
    private DigitsSprites $scoreText;
    private Camera $camera;
    private TextureRepository $textureRepository;
    private string $state = self::MENU;
    /** @var array<string, Sprite>  */
    private array $sprites = [];
    private Animation $menuAnimation;
    private \SDL_Rect $menuPosition;
    private Score $score;
    private ?Pod $player;

    public function __construct(TextureRepository $textureRepository, Camera $camera, Score $score)
    {
        $this->camera = $camera;
        $this->textureRepository = $textureRepository;
        $this->score = $score;
    }

    public function init(): void
    {
        $digits = [];
        for ($i = 0; $i < 10; $i++) {
            $digits[] = $this->textureRepository['digit'.$i.'.png'];
        }
        $this->scoreText = new DigitsSprites($digits, '0', new Vector2Float(468.0, 5.0));
        $this->scoreText->setTracking(-6);
        $this->scoreText->setAnchor(Anchor:: RightTop());
        $this->sprites["title"] = new Sprite($this->textureRepository["title.png"], Game::WIDTH / 2, Game::HEIGHT / 2);
        $this->sprites["gameover"] = new Sprite($this->textureRepository["over.png"], Game::WIDTH / 2, Game::HEIGHT / 2);
        $this->sprites["life1"] = new Sprite($this->textureRepository["life.png"], 8, 4, Anchor::LeftTop());
        $this->sprites["life2"] = new Sprite($this->textureRepository["life.png"], 48, 4, Anchor::LeftTop());
        $this->sprites["life3"] = new Sprite($this->textureRepository["life.png"], 88, 4, Anchor::LeftTop());

        $images = [];
        for ($i = 0; $i <= 13; $i++) {
            $images[] = $this->textureRepository['space'.$i.'.png'];
        }
        $this->menuAnimation = new Animation($images, 14, true);
        $this->menuAnimation->startAnimation();
        $this->menuPosition = new \SDL_Rect(
            0,
            420,
            $images[0]->getWidth(),
            $images[0]->getHeight()
        );
    }

    public function update(float $deltaTime): void
    {
        $this->menuAnimation->update($deltaTime);
    }

    public function draw(Renderer $renderer): void
    {
        if ($this->state === self::MENU) {
            $this->sprites['title']->draw($renderer);
            $renderer->drawTexture($this->menuAnimation->getCurrentFrame(), $this->menuPosition);
        }
        if ($this->state === self::GAME_OVER) {
            $this->sprites['gameover']->draw($renderer);
        }
        if ($this->state === self::PLAY) {
            $this->scoreText->updateText((string)$this->score->get());
            $this->scoreText->draw($renderer);
            $this->drawLives($renderer);
        }
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

    public function addPlayer(Pod $player): void
    {
        $this->player = $player;
    }

    private function drawLives(Renderer $renderer): void
    {
        if ($this->player === null) {
            return;
        }

        for ($i = 1; $i <= $this->player->getLives(); ++$i) {
            $this->sprites['life'.$i]->draw($renderer);
        }
    }
}