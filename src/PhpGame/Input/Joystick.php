<?php

namespace PhpGame\Input;

use PhpGame\Vector2Float;
use SDL_Joystick;

class Joystick
{
    private SDL_Joystick $joystick;
    private Vector2Float $axes;

    public function __construct(int $joystickNumber = 0)
    {
        $this->joystick = SDL_JoystickOpen($joystickNumber);
    }

    public function __destruct()
    {
        SDL_JoystickClose($this->joystick);
    }

    public function update(): void
    {
        $axisX = SDL_JoystickGetAxis($this->joystick, 0);
        $axisX = ceil($axisX / 327.67) / 100;
        $axisY = SDL_JoystickGetAxis($this->joystick, 1);
        $axisY = ceil($axisY / 327.67) / 100;

        $this->axes = new Vector2Float($axisX, $axisY);
    }

    public function getAxes(): Vector2Float
    {
        return $this->axes;
    }
}
