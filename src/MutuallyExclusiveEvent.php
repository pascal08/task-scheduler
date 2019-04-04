<?php

namespace Pascal\TaskScheduler;

class MutuallyExclusiveEvent implements Runnable
{

    /**
     * @var Event
     */
    private $event;

    /**
     * @var MutexInterface
     */
    private $mutex;

    /**
     * @param \Pascal\TaskScheduler\Event          $event
     * @param \Pascal\TaskScheduler\MutexInterface $mutex
     */
    public function __construct(Event $event, MutexInterface $mutex)
    {
        $this->event = $event;
        $this->mutex = $mutex;
        $this->initialize();
    }

    /**
     * @return void
     */
    public function initialize(): void
    {
        $this->event
            ->then(function () {
                $this->mutex->release($this->mutexKey());
            })->skip(function () {
                return $this->mutex->exists($this->mutexKey());
            });
    }

    /**
     * @return void
     */
    public function run(): void
    {
        if (!$this->mutex->create($this->mutexKey())) {
            return;
        }

        $this->event->run();
    }

    /**
     * @return \Pascal\TaskScheduler\MutexKey
     */
    public function mutexKey(): MutexKey
    {
        return new MutexKey(sha1($this->event->getExpression())); // ToDo: get unique key for command
    }
}
