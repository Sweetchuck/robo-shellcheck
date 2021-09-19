<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Task;

use Sweetchuck\Robo\Shellcheck\OutputParser\VersionOutputParser;

class CliVersionTask extends CliTask
{
    protected array $outputParserAssetNameMapping = [
        'description' => 'shellcheck.version.description',
        'version' => 'shellcheck.version.version',
        'license' => 'shellcheck.version.license',
        'website' => 'shellcheck.version.website',
    ];

    public function __construct()
    {
        $this->taskName = 'ShellCheck - Version';
        $this->outputParserClass = VersionOutputParser::class;
    }

    protected function getCommandOptions(): array
    {
        return [
            'version' => [
                'type' => 'option:flag',
                'value' => true,
            ],
        ] + parent::getCommandOptions();
    }
}
