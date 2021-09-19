<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Task;

class CliCheckFilesTask extends CliCheckTask
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
     * @param string[] $files
     *   Example: <code>['a.bash', 'b.bash'];</code>
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
        $this->taskName = 'ShellCheck - Check files';
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
            'files' => [
                'type' => 'arg:values',
                'value' => $this->getFiles(),
            ],
        ] + parent::getCommandOptions();
    }
}
