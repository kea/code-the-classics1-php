<?php

include __DIR__.'/../../vendor/autoload.php';

$screen = new \PhpGame\SDL\Screen(800, 480, 'Boing');
$sound = new \PhpGame\SoundManager(44100, MIX_DEFAULT_FORMAT, 2, 2048);
$sound->setBaseAssetsPath(__DIR__."/sounds/");

$keyboard = new \PhpGame\Keyboard();

$gameStarter = new \Boing\GameStarter($screen, $sound, $keyboard);

$screen->run($gameStarter, $keyboard);
