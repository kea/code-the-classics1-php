<?php

namespace Cavern;

use PhpGame\ECS\Job;

interface JobsCollectorInterface
{
    /** @return Job[] */
    public function getJobs(): array;
}