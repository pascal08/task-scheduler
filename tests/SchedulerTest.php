<?php

namespace Pascal\TaskScheduler\Tests;

use Pascal\TaskScheduler\CommandInterface;
use Pascal\TaskScheduler\Event;
use Pascal\TaskScheduler\Schedule;
use PHPUnit\Framework\TestCase;

class SchedulerTest extends TestCase
{

    /**
     * @test
     */
    public function it_should_return_all_due_events()
    {
        $schedule = new Schedule();
        $eventMock1 = $this->createMock(Event::class);
        $eventMock1
            ->expects($this->once())
            ->method('isDue')
            ->willReturn(true);
        $schedule->scheduleEvent($eventMock1);
        $eventMock2 = $this->createMock(Event::class);
        $eventMock2
            ->expects($this->once())
            ->method('isDue')
            ->willReturn(false);
        $schedule->scheduleEvent($eventMock2);
        $eventMock3 = $this->createMock(Event::class);
        $eventMock3
            ->expects($this->once())
            ->method('isDue')
            ->willReturn(true);
        $schedule->scheduleEvent($eventMock3);

        $this->assertSame([$eventMock1, $eventMock3], $schedule->dueEvents());
    }

    /**
     * @test
     */
    public function it_should_schedule_a_command()
    {
        $schedule = new Schedule();
        $commandMock1 = $this->createMock(CommandInterface::class);
        $schedule->scheduleCommand($commandMock1);
        $commandMock2 = $this->createMock(CommandInterface::class);
        $schedule->scheduleCommand($commandMock2);
        $commandMock3 = $this->createMock(CommandInterface::class);
        $schedule->scheduleCommand($commandMock3);

        $this->assertCount(3, $schedule->getEvents());
    }

    /**
     * @test
     */
    public function it_should_schedule_an_event()
    {
        $schedule = new Schedule();
        $eventMock1 = $this->createMock(Event::class);
        $schedule->scheduleEvent($eventMock1);
        $eventMock2 = $this->createMock(Event::class);
        $schedule->scheduleEvent($eventMock2);
        $eventMock3 = $this->createMock(Event::class);
        $schedule->scheduleEvent($eventMock3);

        $this->assertCount(3, $schedule->getEvents());
    }

}