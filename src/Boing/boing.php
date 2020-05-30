<?php

include __DIR__.'/../../vendor/autoload.php';

$s = new \PhpGame\SDL\Screen(800, 480, 'Boing');

$gameStarter = new \Boing\GameStarter($s);

$s->run($gameStarter);
