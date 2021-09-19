<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Task;

use Sweetchuck\Robo\Shellcheck\Utils;

class CliCheckInputTask extends CliCheckTask
{
    protected array $outputParserAssetNameMapping = [
        'stdOutput' => 'shellcheck.check.stdOutput',
    ];

    // region files
    protected array $files = [];

    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array $files
     *   Key: string fileName
     *   Value: array
     *   - ?fileName: string
     *   - ?content: null|string
     *   - ?command: null|string
     *   One of the "content" or "command" is required.
     *
     * @return $this
     */
    public function setFiles(array $files)
    {
        $this->files = $files;

        return $this;
    }
    // endregion

    public function __construct()
    {
        parent::__construct();
        $this->taskName = 'ShellCheck - Check stdInput';
        $this->outputParserClass = '';
    }

    public function setOptions(array $options)
    {
        parent::setOptions($options);

        if (array_key_exists('files', $options)) {
            $this->setFiles($options['files']);
        }

        return $this;
    }

    protected function getCommandOptions(): array
    {
        return [
            'read-from-stdInput' => [
                'type' => 'arg:values',
                'value' => ['-'],
            ],
        ] + parent::getCommandOptions();
    }

    protected function runDoIt()
    {
        $logger = $this->logger();

        // @todo Handle the different working directories.
        $exitCode = 0;
        $stdError = '';
        $totalFailures = [];
        foreach ($this->getFiles() as $fileName => $file) {
            $file += ['fileName' => $fileName];
            $this->setShell(Utils::detectShellDialog($file) ?: 'sh');
            $baseCommand = $this->getCommand();
            $contentCommand = $file['command'] ?? sprintf('echo -n %s', escapeshellarg($file['content']));

            $this->command = "$contentCommand | $baseCommand";
            $logger->debug($this->command);

            parent::runDoIt();

            $exitCode = max($this->processExitCode, $exitCode);
            $stdError .= $this->processStdError;
            if ($this->processExitCode < 2) {
                $failures = json_decode($this->processStdOutput, true);
                foreach ($failures as &$failure) {
                    $failure['file'] = $file['fileName'] ?? $fileName;
                }

                $totalFailures = array_merge($totalFailures, $failures);
            }
        }

        $this->processExitCode = $exitCode;
        $this->processStdOutput = json_encode($totalFailures);
        $this->processStdError = $stdError;

        return $this;
    }
}
