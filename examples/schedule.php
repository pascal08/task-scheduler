<?php

namespace Example {

    use Pascal\TaskScheduler\Event;
    use Pascal\TaskScheduler\Schedule;
    use Pascal\TaskScheduler\ScheduleBuilderInterface;
    use Pascal\TaskScheduler\ShellCommand;

    require_once __DIR__ . '/../vendor/autoload.php';

    class SchedulerBuilder implements ScheduleBuilderInterface
    {

        /**
         * @return \Pascal\TaskScheduler\Schedule
         */
        public function build(): Schedule
        {
            $schedule = new Schedule;

            $schedule->scheduleEvent(new Event(new ShellCommand(['ls', '-la'])));

            return $schedule;
        }
    }

    $schedulerBuilder = new SchedulerBuilder();

    $schedule = $schedulerBuilder->build();

    foreach ($schedule->getEvents() as $event) {
        if ($event->isDue(new \DateTime())) {
            $event->run();
        }
    }
}
