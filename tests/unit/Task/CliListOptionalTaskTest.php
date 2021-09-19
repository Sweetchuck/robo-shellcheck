<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Unit\Task;

use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyProcess;

/**
 * @covers \Sweetchuck\Robo\Shellcheck\Task\CliListOptionalTask<extended>
 *
 * @property \Sweetchuck\Robo\Shellcheck\Task\CliListOptionalTask|\Robo\Collection\CollectionBuilder $task
 */
class CliListOptionalTaskTest extends TaskTestBase
{
    protected function initTask()
    {
        $this->task = $this->taskBuilder->taskShellcheckListOptional();
    }

    public function casesGetCommand(): array
    {
        return [
            'basic' => [
                'shellcheck --list-optional',
                [],
            ],
            'with env vars' => [
                "FOO1='bar1' FOO2='bar2' shellcheck --list-optional",
                [
                    'envVars' => [
                        'FOO1' => 'bar1',
                        'FOO2' => 'bar2',
                    ],
                ],
            ],
            'with workingDirectory and env vars' => [
                "cd '/my/dir01' && FOO1='bar1' FOO2='bar2' shellcheck --list-optional",
                [
                    'workingDirectory' => '/my/dir01',
                    'envVars' => [
                        'FOO1' => 'bar1',
                        'FOO2' => 'bar2',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesGetCommand
     */
    public function testGetCommand(string $expected, array $options): void
    {
        $this->task->setOptions($options);
        $this->tester->assertSame($expected, $this->task->getCommand());
    }

    public function testRunSuccess(): void
    {
        $expected = [
            'exitCode' => 0,
            'data' => [
                'abc.shellcheck.optional.rules' => [
                    'add-default-case' => [
                        'name' => 'add-default-case',
                        'desc' => 'Suggest adding a default case in `case` statements',
                        'example' => "case $? in 0) echo 'Success';; esac",
                        'fix' => "case $? in 0) echo 'Success';; *) echo 'Fail' ;; esac",
                    ],
                    'avoid-nullary-conditions' => [
                        'name' => 'avoid-nullary-conditions',
                        'desc' => 'Suggest explicitly using -n in `[ $var ]`',
                        'example' => '[ "$var" ]',
                        'fix' => '[ -n "$var" ]',
                    ],
                ],
            ],
            'log' => [
                [
                    'notice',
                    'runs "<info>{command}</info>"',
                    [
                        'command' => 'shellcheck --list-optional',
                        'name' => 'ShellCheck - List optional rules',
                    ],
                ],
            ],
        ];

        $options = [
            'assetNamePrefix' => 'abc.',
        ];

        $this->task->setOptions($options);

        $processIndex = count(DummyProcess::$instances);

        DummyProcess::$prophecy[$processIndex] = [
            'exitCode' => 0,
            'stdOutput' => implode(PHP_EOL, [
                'name:    add-default-case',
                'desc:    Suggest adding a default case in `case` statements',
                "example: case $? in 0) echo 'Success';; esac",
                "fix:     case $? in 0) echo 'Success';; *) echo 'Fail' ;; esac",
                '',
                'name:    avoid-nullary-conditions',
                'desc:    Suggest explicitly using -n in `[ $var ]`',
                'example: [ "$var" ]',
                'fix:     [ -n "$var" ]',
                '',
            ]),
            'stdError' => '',
        ];

        $result = $this->task->run();

        if (array_key_exists('exitCode', $expected)) {
            $this->tester->assertSame(
                $expected['exitCode'],
                $result->getExitCode(),
                'Exit code is different than the expected.',
            );
        }

        if (array_key_exists('data', $expected)) {
            $actualData = $result->getData();
            foreach ($expected['data'] as $key => $expectedValue) {
                $this->tester->assertSame(
                    $expectedValue,
                    $actualData[$key],
                    'Provided assets are same as expected',
                );
            }
        }

        if (array_key_exists('log', $expected)) {
            /** @var \Symfony\Component\ErrorHandler\BufferingLogger $logger */
            $logger = $this->task->logger();
            $logEntries = $logger->cleanLogs();
            foreach ($expected['log'] as $key => $expectedLog) {
                $actualLog = $logEntries[$key];
                unset($actualLog[2]['task']);
                $this->tester->assertSame($expectedLog, $actualLog);
            }
        }
    }
}
