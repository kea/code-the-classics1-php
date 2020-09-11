<?php

use Boing\GameStarter;
use PhpGame\Input\ArrowKeysBinding;
use PhpGame\Input\ButtonAction;
use PhpGame\Input\ButtonBinding;
use PhpGame\Input\DirectionBinding;
use PhpGame\Input\InputActions;
use PhpGame\Input\Keyboard;
use PhpGame\Input\VectorAction;
use PhpGame\Input\WASDKeysBinding;
use PhpGame\SDL\Engine;
use PhpGame\SDL\Renderer;
use PhpGame\SDL\Screen;
use PhpGame\SoundManager;

include __DIR__.'/../../vendor/autoload.php';

$screen = new Screen(800, 480, 'Boing');
$screen->setIcon(__DIR__.'/../../phpgame_small.bmp');
$renderer = new Renderer($screen->getWindow());

$sound = new SoundManager(44100, MIX_DEFAULT_FORMAT, 2, 2048);
$sound->setAssetsPath(__DIR__);

$inputActions = new InputActions(
    [
        'MoveP1' => new VectorAction([new ArrowKeysBinding(), new WASDKeysBinding()]),
        'MoveP2' => new VectorAction(
            [
                new DirectionBinding(
                    [
                        DirectionBinding::Up => \SDL_SCANCODE_K,
                        DirectionBinding::Down => \SDL_SCANCODE_M,
                        DirectionBinding::Left => 0,
                        DirectionBinding::Right => 0,
                    ]
                ),
            ]
        ),
        'Fire' => new ButtonAction([new ButtonBinding([\SDL_SCANCODE_SPACE])]),
        'MenuUp' => new ButtonAction([new ButtonBinding([\SDL_SCANCODE_UP])]),
        'MenuDown' => new ButtonAction([new ButtonBinding([\SDL_SCANCODE_DOWN])]),
    ],
    new Keyboard()
);

$gameStarter = new GameStarter($screen->getWidth(), $screen->getHeight(), $sound, $inputActions);

$engine = new Engine($renderer, $inputActions);
$engine->run($gameStarter);
