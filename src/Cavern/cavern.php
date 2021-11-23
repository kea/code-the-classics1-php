<?php

use Cavern\GameStarter;
use PhpGame\Camera;
use PhpGame\Input\ArrowKeysBinding;
use PhpGame\Input\ButtonAction;
use PhpGame\Input\ButtonBinding;
use PhpGame\Input\InputActions;
use PhpGame\Input\Keyboard;
use PhpGame\Input\VectorAction;
use PhpGame\Input\WASDKeysBinding;
use PhpGame\SDL\Engine;
use PhpGame\SDL\Renderer;
use PhpGame\SDL\Screen;
use PhpGame\SoundManager;
use PhpGame\TextureRepository;

include __DIR__.'/../../vendor/autoload.php';

$screen = new Screen(800, 480, 'Cavern');
$screen->setIcon(__DIR__.'/../../phpgame_small.bmp');
$camera = new Camera(new \SDL_Rect(0,0, $screen->getWidth(), $screen->getHeight()));
$renderer = new Renderer($screen->getWindow(), $camera);

$sound = new SoundManager(44100, MIX_DEFAULT_FORMAT, 2, 2048);
$sound->setAssetsPath(__DIR__);

$inputActions = new InputActions(
    [
        'Move' => new VectorAction([new ArrowKeysBinding(), new WASDKeysBinding()]),
        'Fire' => new ButtonAction([new ButtonBinding([SDL_SCANCODE_SPACE])]),
    ],
    new Keyboard()
);

$textureRepository = new TextureRepository($renderer, __DIR__.DIRECTORY_SEPARATOR.'images');
$gameStarter = new GameStarter($screen->getWidth(), $screen->getHeight(), $sound, $inputActions, $textureRepository);

$engine = new Engine($renderer, $inputActions);
$engine->run($gameStarter);
