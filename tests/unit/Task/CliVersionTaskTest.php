<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Unit\Task;

use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyProcess;

/**
 * @covers \Sweetchuck\Robo\Shellcheck\Task\CliVersionTask<extended>
 *
 * @property \Sweetchuck\Robo\Shellcheck\Task\CliVersionTask|\Robo\Collection\CollectionBuilder $task
 */
class CliVersionTaskTest extends TaskTestBase
{
    protected function initTask()
    {
        $this->task = $this->taskBuilder->taskShellcheckVersion();
    }

    public function casesGetCommand(): array
    {
        return [
            'basic' => [
                'shellcheck --version',
                [],
            ],
            'with env vars' => [
                "FOO1='bar1' FOO2='bar2' shellcheck --version",
                [
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
                'abc.shellcheck.version.description' => 'ShellCheck - shell script analysis tool',
                'abc.shellcheck.version.version' => '0.7.1',
                'abc.shellcheck.version.license' => 'GNU General Public License, version 3',
                'abc.shellcheck.version.website' => 'https://www.shellcheck.net',
            ],
            'log' => [
                [
                    'notice',
                    'runs "<info>{command}</info>"',
                    [
                        'command' => 'shellcheck --version',
                        'name' => 'ShellCheck - Version',
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
                'ShellCheck - shell script analysis tool',
                'version: 0.7.1',
                'license: GNU General Public License, version 3',
                'website: https://www.shellcheck.net',
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
