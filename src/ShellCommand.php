<?php

namespace Pascal\TaskScheduler;

use Symfony\Component\Process\Process;

class ShellCommand extends AbstractCommand
{

    /**
     * @var array
     */
    private $command;

    /**
     * @var string|null
     */
    private $workingDirectory;

    /**
     * @var array|null
     */
    private $environmentVariables;

    /**
     * @var string|null
     */
    private $input;

    /**
     * @var float|null
     */
    private $timeout;

    /**
     * @param array       $command
     * @param string|null $workingDirectory
     * @param array|null  $environmentVariables
     * @param string|null $input
     * @param float|null    $timeout
     */
    public function __construct(
        array $command,
        string $workingDirectory = null,
        array $environmentVariables = null,
        string $input = null,
        float $timeout = null
    ) {
        $this->command = $command;
        $this->workingDirectory = $workingDirectory;
        $this->environmentVariables = $environmentVariables;
        $this->input = $input;
        $this->timeout = $timeout;
    }

    /**
     * @return Process
     */
    public function run(): Process
    {
        $process = new Process(
            $this->command,
            $this->workingDirectory,
            $this->environmentVariables,
            $this->input,
            $this->timeout
        );

        $this->runProcess($process);

        return $process;
    }
}
