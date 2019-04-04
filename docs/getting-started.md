# Getting started with Pascal/Container

## Prerequisites

 - PHP 7.2 or higher

## Installation

Install Pascal/TaskScheduler using Composer:

```bash
composer require pascal08/task-scheduler
```

## Start using it!

The task scheduler is used in 3 simple steps:

1. Create a SchedulerBuilder containing the scheduled events

```php
class SchedulerBuilder implements ScheduleBuilderInterface
{

    /**
     * @return \Pascal\TaskScheduler\Schedule
     */
    public function build(): Schedule
    {
        $schedule = new Schedule;

        $schedule->addEvent(new Event(new ShellCommand(['ls', '-la']), '0 0 * * *')); // Run `ls -la` every day at 00:00

        return $schedule;
    }
}
```

2. Create a script that instantiates ScheduleBuilder, builds the schedule and runs events that are due

```php
// example.php

$schedulerBuilder = new SchedulerBuilder();

$schedule = $schedulerBuilder->build();

foreach ($schedule->getEvents() as $event) {
    if ($event->isDue(new \DateTime())) {
        echo $event->run();
    }
}
 ```
 
 3. Add this Cron entry:
 
 ```
 * * * * * /usr/bin/php /var/www/example.php >> /dev/null 2>&1
 ```
 
 