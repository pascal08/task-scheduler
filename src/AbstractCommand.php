<?php

namespace Pascal\TaskScheduler;

use Symfony\Component\Process\Process;

abstract class AbstractCommand implements CommandInterface
{

    /**
     * @var bool
     */
    protected $waitForTermination = true;

    /**
     * @var int
     */
    protected $timeoutAfter = 0;

    /**
     * @return \Pascal\TaskScheduler\AbstractCommand
     */
    public function doNotWaitForTermination(): self
    {
        $this->waitForTermination = false;

        return $this;
    }

    /**
     * @param int $seconds
     *
     * @return \Pascal\TaskScheduler\AbstractCommand
     */
    public function timeoutAfter(int $seconds = 0): self
    {
        if ($seconds > 0) {
            $this->timeoutAfter = $seconds;
        }

        return $this;
    }

    /**
     * @param \Symfony\Component\Process\Process $process
     */
    protected function runProcess(Process $process): void
    {
        if ($this->timeoutAfter) {
            $process->setTimeout($this->timeoutAfter);
        }

        $this->waitForTermination ? $process->run() : $process->start();
    }
}
