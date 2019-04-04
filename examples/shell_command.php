<?php

use Pascal\TaskScheduler\Event;
use Pascal\TaskScheduler\ShellCommand;

require_once __DIR__ . '/../vendor/autoload.php';

$event = new Event(new ShellCommand(['whoami']));

echo $event->run();
