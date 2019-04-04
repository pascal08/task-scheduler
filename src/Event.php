<?php

namespace Pascal\TaskScheduler;

use Closure;
use Cron\CronExpression;
use DateTime;

class Event implements Runnable
{

    /**
     * The scheduled command.
     *
     * @var \Pascal\TaskScheduler\CommandInterface
     */
    private $command;

    /**
     * The cron expression representing the event's frequency.
     *
     * @var string
     */
    private $expression = '* * * * *';

    /**
     * @var array
     */
    private $afterCallbacks = [];

    /**
     * @var array
     */
    private $rejects = [];

    /**
     * @param \Pascal\TaskScheduler\CommandInterface $command
     * @param string|null                            $expression
     */
    public function __construct(
        CommandInterface $command,
        string $expression = null
    ) {
        $this->command = $command;
        $this->expression = $expression ?: $this->expression;
    }

    /**
     * The Cron expression representing the event's frequency.
     *
     * @param \Cron\CronExpression $expression
     *
     * @return $this
     */
    public function cron(CronExpression $expression)
    {
        $this->expression = $expression->getExpression() ?: $this->expression;

        return $this;
    }

    /**
     * Get the Cron expression for the event.
     *
     * @return CronExpression
     */
    public function getExpression(): CronExpression
    {
        return CronExpression::factory($this->expression);
    }

    /**
     * Determine if the given event should run based on the Cron expression.
     *
     * @param \DateTime $dateTime
     *
     * @return bool
     */
    public function isDue(DateTime $dateTime = null): bool
    {
        return CronExpression::factory($this->expression)->isDue($dateTime ?: 'now');
    }

    /**
     * Run the command.
     *
     * @return void
     */
    public function run(): void
    {
        foreach ($this->rejects as $callback) {
            if (call_user_func($callback)) {
                return;
            }
        }

        $this->command->run();

        foreach ($this->afterCallbacks as $callback) {
            call_user_func($callback);
        }
    }

    /**
     * Register a callback to be called after the operation.
     *
     * @param  \Closure $callback
     *
     * @return $this
     */
    public function then(Closure $callback)
    {
        $this->afterCallbacks[] = $callback;

        return $this;
    }

    /**
     * Register a callback to further filter the schedule.
     *
     * @param  \Closure|bool  $callback
     * @return $this
     */
    public function skip($callback)
    {
        $this->rejects[] = is_callable($callback) ? $callback : function () use ($callback) {
            return $callback;
        };

        return $this;
    }
}
