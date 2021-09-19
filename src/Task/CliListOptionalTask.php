<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Task;

use Sweetchuck\Robo\Shellcheck\OutputParser\RuleInfoListOutputParser;

class CliListOptionalTask extends CliTask
{
    protected array $outputParserAssetNameMapping = [
        'rules' => 'shellcheck.optional.rules',
    ];

    public function __construct()
    {
        $this->taskName = 'ShellCheck - List optional rules';
        $this->outputParserClass = RuleInfoListOutputParser::class;
    }

    protected function getCommandOptions(): array
    {
        return [
            'list-optional' => [
                'type' => 'option:flag',
                'value' => true,
            ],
        ] + parent::getCommandOptions();
    }
}
