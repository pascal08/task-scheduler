<?php

namespace Pascal\TaskScheduler;

use Symfony\Component\Process\Process;

interface CommandInterface
{

    /**
     * @return Process
     */
    public function run(): Process;
}
