<?php

namespace Pascal\TaskScheduler\Tests;

use Pascal\TaskScheduler\ShellCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

class ShellCommandTest extends TestCase
{

    /** @test */
    public function it_should_tell_if_a_shell_command_was_run_successfully()
    {
        $shellCommand = new ShellCommand(['whoami']);

        $process = $shellCommand->run();

        $this->assertTrue($process->isSuccessful());
    }

    /** @test */
    public function it_should_tell_if_a_shell_command_has_failed()
    {
        $shellCommand = new ShellCommand(['let "var1 = 1/0"']);

        $process = $shellCommand->run();

        $this->assertFalse($process->isSuccessful());
    }

    /** @test */
    public function it_should_return_the_output_of_a_shell_command()
    {
        $shellCommand = new ShellCommand(['echo', '-n', 'Hello world!']);

        $process = $shellCommand->run();

        $this->assertEquals('Hello world!', $process->getOutput());
    }

    /** @test */
    public function it_should_run_a_shell_command_and_wait_for_its_termination()
    {
        $shellCommand = new ShellCommand(['sleep', '1']);

        $process = $shellCommand->run();

        $this->assertTrue($process->isTerminated());
    }

    /** @test */
    public function it_should_run_a_shell_command_and_not_wait_for_its_termination()
    {
        $shellCommand = new ShellCommand(['sleep', '1']);

        $process = $shellCommand->doNotWaitForTermination()->run();

        $this->assertFalse($process->isTerminated());
    }

    /** @test */
    public function it_should_run_a_shell_command_multiple_times_successfully()
    {
        $shellCommand = new ShellCommand(['whoami']);

        $process1 = $shellCommand->run();
        $process2 = $shellCommand->run();

        $this->assertNotSame($process1, $process2);
        $this->assertTrue($process1->isSuccessful());
        $this->assertTrue($process2->isSuccessful());
    }

    /** @test */
    public function it_should_timeout_the_shell_command_after_running_n_seconds()
    {
        $this->expectException(ProcessTimedOutException::class);

        $shellCommand = new ShellCommand(['sleep', '5']);

        $shellCommand->timeoutAfter(1)->run();
    }

}