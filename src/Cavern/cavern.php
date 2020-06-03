<?php

use Cavern\GameStarter;
use PhpGame\Keyboard;
use PhpGame\SDL\Engine;
use PhpGame\SDL\Screen;
use PhpGame\SoundManager;

include __DIR__.'/../../vendor/autoload.php';

$screen = new Screen(800, 480, 'Cavern');

$sound = new SoundManager(44100, MIX_DEFAULT_FORMAT, 2, 2048);
$sound->setBaseAssetsPath(__DIR__."/sounds/");

$keyboard = new Keyboard();

$gameStarter = new GameStarter($screen->getWidth(), $screen->getHeight(), $sound, $keyboard);

$engine = new Engine($screen, $keyboard);
$engine->run($gameStarter);
