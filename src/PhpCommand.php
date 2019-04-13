<?php

namespace Pascal\TaskScheduler;

use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\Process;

class PhpCommand extends AbstractCommand
{

    /**
     * @var array
     */
    private static $phpTags = ['<?php', '<?'];

    /**
     * @var string
     */
    private $script;

    /**
     * @var string|null
     */
    private $workingDirectory;

    /**
     * @var array|null
     */
    private $environmentVariables;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var array|null
     */
    private $php;

    /**
     * @param string      $script
     * @param string|null $workingDirectory
     * @param array|null  $environmentVariables
     * @param int         $timeout
     * @param array|null  $php
     */
    public function __construct(
        string $script,
        string $workingDirectory = null,
        array $environmentVariables = null,
        int $timeout = 60,
        array $php = null
    ) {
        $this->script = $this->isFile($script) ? $this->getFileContent($script) : $script;
        $this->guardAgainstInvalidScript();
        $this->workingDirectory = $workingDirectory;
        $this->environmentVariables = $environmentVariables;
        $this->timeout = $timeout;
        $this->php = $php;
    }

    /**
     * @return Process
     */
    public function run(): Process
    {
        $process = new PhpProcess(
            $this->script,
            $this->workingDirectory,
            $this->environmentVariables,
            $this->timeout,
            $this->php
        );

        $this->runProcess($process);

        return $process;
    }

    /**
     * @param string $script
     *
     * @return bool
     */
    private function isPhpScript(string $script): bool
    {
        foreach (static::$phpTags as $phpTag) {
            if (substr(ltrim($script), 0, strlen($phpTag)) === '<?php') {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws InvalidPhpScript
     */
    private function guardAgainstInvalidScript(): void
    {
        if (!$this->isPhpScript($this->script)) {
            throw new InvalidPhpScript();
        }
    }

    /**
     * @param string $script
     *
     * @return bool
     */
    private function isFile(string $script): bool
    {
        return is_file($script);
    }

    /**
     * @param string $script
     *
     * @return string
     */
    private function getFileContent(string $script): string
    {
        $contents = file_get_contents($script);

        if($contents === false) {
            throw new CouldNotReadFileException();
        }

        return $contents;
    }
}
