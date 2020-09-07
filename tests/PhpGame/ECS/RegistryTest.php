<?php

namespace PhpGame\ECS;

use PhpGame\ECS\Component\Gravity;
use PhpGame\ECS\Component\Transform;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{
    /** @test */
    public function createAnEntity()
    {
        $registry = new Registry();
        $entity = $registry->create();

        self::assertInstanceOf(Entity::class, $entity);
    }

    /** @test */
    public function addAndGetComponent()
    {
        $registry = new Registry();
        $entity0 = $registry->create();
        $transform0 = new Transform();
        $transform0->x = .0;
        $transform0->y = .0;
        $registry->add($entity0, $transform0);
        $entity1 = $registry->create();
        $transform1 = new Transform();
        $transform1->x = .1;
        $transform1->y = .1;
        $gravity = new Gravity();
        $gravity->velocity = .0;
        $gravity->acceleration = 9.8;
        $registry->add($entity1, $transform1);
        $registry->add($entity1, $gravity);

        self::assertSame(
            [(string)$entity0 => [Transform::class => $transform0], (string)$entity1 => [Transform::class => $transform1]],
            $registry->get([Transform::class])
        );
        self::assertSame(
            [(string)$entity1 => [Transform::class => $transform1, Gravity::class => $gravity]],
            $registry->get([Transform::class, Gravity::class])
        );
        self::assertSame(
            [(string)$entity1 => [Gravity::class => $gravity, Transform::class => $transform1]],
            $registry->get([Gravity::class, Transform::class])
        );
        self::assertSame([], $registry->get(['\Other\Component\Class']));
    }
}
