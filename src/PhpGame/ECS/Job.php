<?php

namespace PhpGame\ECS;

interface Job
{
    public function __invoke(Registry $registry);
}