<?php

namespace Pascal\TaskScheduler\Tests;

use Pascal\TaskScheduler\CommandInterface;
use Pascal\TaskScheduler\Event;
use Pascal\TaskScheduler\MutexInterface;
use Pascal\TaskScheduler\MutuallyExclusiveEvent;
use PHPUnit\Framework\TestCase;

class MutuallyExclusiveEventTest extends TestCase
{

    /**
     * @test
     */
    public function it_should_run_the_event_if_no_mutex_exists_for_this_event()
    {
        $commandMock = $this->createMock(CommandInterface::class);
        $commandMock
            ->expects($this->once())
            ->method('run');
        $event = new Event($commandMock);

        $mutexStore = $this->createMock(MutexInterface::class);
        $mutexStore
            ->method('create')
            ->willReturn(true);
        $mutexStore
            ->expects($this->once())
            ->method('exists')
            ->willReturn(false);
        $mutexStore
            ->expects($this->once())
            ->method('release')
            ->willReturn(true);

        $event = $this->createMutuallyExclusiveEvent($event, $mutexStore);

        $event->run();
    }

    /**
     * @test
     */
    public function it_should_not_run_the_event_if_the_mutex_was_not_created()
    {
        $commandMock = $this->createMock(CommandInterface::class);
        $commandMock
            ->expects($this->never())
            ->method('run');
        $event = new Event($commandMock);

        $mutexStore = $this->createMock(MutexInterface::class);
        $mutexStore
            ->expects($this->once())
            ->method('create')
            ->willReturn(false);

        $event = $this->createMutuallyExclusiveEvent($event, $mutexStore);

        $event->run();
    }

    /**
     * @test
     */
    public function it_should_not_run_the_event_if_the_mutex_exists_for_this_event()
    {
        $eventMock = $this->createMock(Event::class);
        $eventMock
            ->expects($this->never())
            ->method('run');
        $eventMock
            ->method('then')
            ->willReturn($eventMock);
        $eventMock
            ->method('skip')
            ->willReturn($eventMock);

        $mutexStore = $this->createMock(MutexInterface::class);
        $mutexStore
            ->method('create')
            ->willReturn(false);
        $mutexStore
            ->method('exists')
            ->willReturn(true);

        $event = $this->createMutuallyExclusiveEvent($eventMock, $mutexStore);

        $event->run();
    }

    /**
     * @param \Pascal\TaskScheduler\Event          $event
     * @param \Pascal\TaskScheduler\MutexInterface $mutexStore
     *
     * @return \Pascal\TaskScheduler\MutuallyExclusiveEvent
     */
    private function createMutuallyExclusiveEvent(Event $event, MutexInterface $mutexStore): MutuallyExclusiveEvent
    {
        return new MutuallyExclusiveEvent($event, $mutexStore);
    }
}