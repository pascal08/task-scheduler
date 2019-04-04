<?php

namespace Pascal\TaskScheduler;

use DateTime;

class Schedule
{

    /**
     * @var Event[]
     */
    private $events = [];

    /**
     * Get all of the events on the schedule that are due.
     *
     * @param \DateTime $currentTime
     *
     * @return Event[]
     */
    public function dueEvents(DateTime $currentTime = null): array
    {
        $dueEvents = [];
        foreach ($this->events as $event) {
            if ($event->isDue($currentTime)) {
                $dueEvents[] = $event;
            }
        }

        return $dueEvents;
    }

    /**
     * Add command as an event to the schedule.
     *
     * @param \Pascal\TaskScheduler\CommandInterface $command
     *
     * @return \Pascal\TaskScheduler\Event
     */
    public function scheduleCommand(CommandInterface $command)
    {
        $this->events[] = $event = new Event($command);

        return $event;
    }

    /**
     * @param \Pascal\TaskScheduler\Event $event
     */
    public function scheduleEvent(Event $event)
    {
        $this->events[] = $event;
    }

    /**
     * @return \Pascal\TaskScheduler\Event[]
     */
    public function getEvents(): array
    {
        return $this->events;
    }
}
