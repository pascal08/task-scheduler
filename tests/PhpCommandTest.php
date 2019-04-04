<?php

namespace Pascal\TaskScheduler\Tests;

use Pascal\TaskScheduler\InvalidPhpScript;
use Pascal\TaskScheduler\PhpCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ProcessTimedOutException;

class PhpCommandTest extends TestCase
{

    /** @test */
    public function it_should_tell_if_a_php_script_exited_successfully()
    {
        $phpCommand = new PhpCommand('<?php $a = 1; $b = 1; $c = $a + $b; echo $c; ?>');

        $process = $phpCommand->run();

        $this->assertTrue($process->isSuccessful());
    }

    /** @test */
    public function it_should_tell_if_a_php_script_exited_unsuccessfully()
    {
        $phpCommand = new PhpCommand('<?php exit(1); ?>');

        $process = $phpCommand->run();

        $this->assertFalse($process->isSuccessful());
    }

    /** @test */
    public function it_should_return_the_output_of_a_php_script()
    {
        $expected = 'Hello World!';

        $phpCommand = new PhpCommand('<?php echo "' . $expected . '";');

        $process = $phpCommand->run();

        $this->assertEquals($expected, $process->getOutput());
    }

    /** @test */
    public function it_should_run_a_php_script_and_wait_for_its_termination()
    {
        $phpCommand = new PhpCommand('<?php usleep(1000); ?>');

        $process = $phpCommand->run();

        $this->assertTrue($process->isTerminated());
    }

    /** @test */
    public function it_should_run_a_php_script_and_not_wait_for_its_termination()
    {
        $phpCommand = new PhpCommand('<?php usleep(1000); ?>');

        $process = $phpCommand->doNotWaitForTermination()->run();

        $this->assertFalse($process->isTerminated());
    }

    /** @test */
    public function it_should_run_a_php_script_multiple_times_successfully()
    {
        $phpCommand = new PhpCommand('<?php $a = 1; $b = 1; $c = $a + $b; echo $c; ?>');

        $process1 = $phpCommand->run();
        $process2 = $phpCommand->run();

        $this->assertNotSame($process1, $process2);
        $this->assertTrue($process1->isSuccessful());
        $this->assertTrue($process2->isSuccessful());
    }

    /** @test */
    public function it_should_timeout_the_shell_command_after_running_n_seconds()
    {
        $this->expectException(ProcessTimedOutException::class);

        $phpCommand = new PhpCommand('<?php usleep(5000000); ?>');

        $phpCommand->timeoutAfter(1)->run();
    }

    /** @test */
    public function it_should_run_a_php_script_from_a_file()
    {
        $phpCommand = new PhpCommand(__DIR__ . '/demo-php-script.php');

        $process = $phpCommand->run();

        $this->assertTrue($process->isSuccessful());
    }

    /** @test */
    public function it_should_throw_an_exception_for_a_non_existing_file()
    {
        $this->expectException(InvalidPhpScript::class);

        $phpCommand = new PhpCommand(__DIR__ . '/a-not-existing-file.php');

        $process = $phpCommand->run();

        $this->assertTrue($process->isSuccessful());
    }

    /** @test */
    public function it_should_throw_an_exception_for_an_invalid_php_script()
    {
        $this->expectException(InvalidPhpScript::class);

        $phpCommand = new PhpCommand('this is not a php script');

        $phpCommand->run();
    }

}