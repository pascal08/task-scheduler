<?php

namespace Pascal\TaskScheduler;

interface ScheduleBuilderInterface
{

    /**
     * @return \Pascal\TaskScheduler\Schedule
     */
    public function build(): Schedule;
}
