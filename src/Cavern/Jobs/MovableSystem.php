<?php

namespace Cavern\Jobs;

use PhpGame\ECS\Component\Movable;
use PhpGame\ECS\Component\Transform;
use PhpGame\ECS\Job;
use PhpGame\ECS\Registry;

class MovableSystem implements Job
{
    public function __invoke(Registry $registry)
    {
        /** @todo pass to the function or set to the object */
        $deltaTime = 0.012;
        $components = $registry->get([Movable::class, Transform::class]);

        foreach ($components as $component) {
            /** @var Movable $movable */
            $movable = $component[Movable::class];
            /** @var Transform $transform */
            $transform = $component[Transform::class];

            $this->move($movable, $transform, $deltaTime);
        }
    }

    public function move(Movable $movable, Transform $transform, float $deltaTime): void
    {
        $frameSpeed = $movable->speed * $deltaTime;
        $newPosition = clone $transform;

        $newPosition->x += $movable->direction->x * $frameSpeed;
        $newPosition->y += $movable->direction->y * $frameSpeed;

        if ($newPosition->x < 70 || $newPosition->x > 730) {
            return;
        }

        $transform->x = $newPosition->x;
        $transform->y = $newPosition->y;
    }
}