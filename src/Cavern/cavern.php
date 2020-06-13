<?php

use Cavern\GameStarter;
use PhpGame\Input\ArrowKeysBinding;
use PhpGame\Input\ButtonAction;
use PhpGame\Input\ButtonBinding;
use PhpGame\Input\InputActions;
use PhpGame\Input\VectorAction;
use PhpGame\Input\Keyboard;
use PhpGame\Input\WASDKeysBinding;
use PhpGame\SDL\Engine;
use PhpGame\SDL\Renderer;
use PhpGame\SDL\Screen;
use PhpGame\SoundManager;

include __DIR__.'/../../vendor/autoload.php';

$screen = new Screen(800, 480, 'Cavern');
$screen->setIcon(__DIR__.'/../../phpgame_small.bmp');
$renderer = new Renderer($screen->getWindow());

$sound = new SoundManager(44100, MIX_DEFAULT_FORMAT, 2, 2048);
$sound->setBaseAssetsPath(__DIR__);

$keyboard = new Keyboard();
$inputActions = new InputActions(
    [
        'Move' => new VectorAction([new ArrowKeysBinding(), new WASDKeysBinding()]),
        'Fire' => new ButtonAction([new ButtonBinding([\SDL_SCANCODE_SPACE])]),
    ]
);

$gameStarter = new GameStarter($screen->getWidth(), $screen->getHeight(), $sound, $keyboard);

$engine = new Engine($renderer, $inputActions, $keyboard);
$engine->run($gameStarter);
