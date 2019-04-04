<?php

namespace Pascal\TaskScheduler;

interface Runnable
{

    /**
     * @return void
     */
    public function run(): void;
}
