<?php

namespace Pascal\TaskScheduler\Tests;

use Closure;
use Cron\CronExpression;
use DateTime;
use Pascal\TaskScheduler\CommandInterface;
use Pascal\TaskScheduler\Event;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{

    /**
     * @test
     */
    public function it_should_read_a_cron_expression()
    {
        $commandMock = $this->createMock(CommandInterface::class);
        $event = new Event($commandMock);
        $cronExpression = CronExpression::factory('* * * * *');
        $event->cron($cronExpression);

        $this->assertEquals($cronExpression, $event->getExpression());
    }

    /**
     * @test
     * @dataProvider dueEventsProvider
     */
    public function it_should_determine_if_an_event_is_due(string $cronExpression, DateTime $dateTime)
    {
        $commandMock = $this->createMock(CommandInterface::class);
        $event = new Event($commandMock);
        $cronExpression = CronExpression::factory($cronExpression);
        $event->cron($cronExpression);

        $this->assertTrue($event->isDue($dateTime));
    }

    public function dueEventsProvider()
    {
        return [
            ['* * * * *', new DateTime],
            ['0 1 * * *', new DateTime('01:00')],
            ['0 1 * * *', new DateTime('01:00:01')],
            ['0 1 * * *', new DateTime('01:00:59')],
            ['20 5 1 * *', new DateTime('2018-12-01 05:20:00')],
            ['55 11 * * 4', new DateTime('2018-11-01 11:55:00')],
            ['7 18 * * fri', new DateTime('2020-05-29 18:07:00')],
            ['0 1 * 10 *', new DateTime('2020-10-01 01:00:00')]
        ];
    }

    /**
     * @test
     * @dataProvider notDueEventsProvider
     */
    public function it_should_determine_if_an_event_is_not_due(string $cronExpression, DateTime $dateTime)
    {
        $commandMock = $this->createMock(CommandInterface::class);
        $event = new Event($commandMock);
        $cronExpression = CronExpression::factory($cronExpression);
        $event->cron($cronExpression);

        $this->assertFalse($event->isDue($dateTime));
    }

    public function notDueEventsProvider()
    {
        return [
            ['0 1 * * *', new DateTime('00:59:59')],
            ['0 1 * * *', new DateTime('01:01:00')],
            ['20 5 1 * *', new DateTime('2018-12-02 05:20:00')],
            ['55 11 * * 4', new DateTime('2018-11-01 11:54:00')],
            ['7 18 * * fri', new DateTime('2020-05-30 18:07:00')],
            ['0 1 * 10 *', new DateTime('2020-10-01 02:00:00')]
        ];
    }

    /**
     * @test
     */
    public function it_should_run_the_event()
    {
        $commandMock = $this->createMock(CommandInterface::class);
        $commandMock
            ->expects($this->once())
            ->method('run');
        $event = new Event($commandMock);

        $event->run();
    }

    /**
     * @test
     */
    public function it_should_skip_the_event_given_a_callback()
    {
        $commandMock = $this->createMock(CommandInterface::class);
        $commandMock
            ->expects($this->never())
            ->method('run');
        $event = new Event($commandMock);

        $event->skip(function() {
            return true;
        });

        $event->run();
    }

    /**
     * @test
     */
    public function it_should_not_skip_the_event_given_a_falsy_callback()
    {
        $commandMock = $this->createMock(CommandInterface::class);
        $commandMock
            ->expects($this->once())
            ->method('run');
        $event = new Event($commandMock);

        $event->skip(function() {
            return false;
        });

        $event->run();
    }

    /**
     * @test
     */
    public function it_should_skip_the_event_given_a_truthy_boolean_condition()
    {
        $commandMock = $this->createMock(CommandInterface::class);
        $commandMock
            ->expects($this->never())
            ->method('run');
        $event = new Event($commandMock);

        $event->skip(true);

        $event->run();
    }

    /**
     * @test
     */
    public function it_should_not_skip_the_event_given_a_falsy_boolean_condition()
    {
        $commandMock = $this->createMock(CommandInterface::class);
        $commandMock
            ->expects($this->once())
            ->method('run');
        $event = new Event($commandMock);

        $event->skip(false);

        $event->run();
    }

    /**
     * @test
     */
    public function it_should_run_callbacks_after_successfully_running_the_event()
    {
        $commandMock = $this->createMock(CommandInterface::class);
        $commandMock
            ->expects($this->once())
            ->method('run');
        $event = new Event($commandMock);

        $closureFunc = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['__invoke'])
            ->getMock();

        $closureFunc->expects($this->once())
            ->method('__invoke');

        $afterCallback = function() use ($closureFunc) {
            call_user_func($closureFunc);
        };

        $event->then($afterCallback);

        $event->run();
    }
}