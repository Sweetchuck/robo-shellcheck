<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Unit\Task;

use Sweetchuck\Codeception\Module\RoboTaskRunner\DummyProcess;
use Sweetchuck\Robo\Shellcheck\LintReportWrapper\ReportWrapper;
use Sweetchuck\Robo\Shellcheck\ResultConverter\JsonToReport;

/**
 * @covers \Sweetchuck\Robo\Shellcheck\Task\CliCheckFilesTask<extended>
 *
 * @property \Sweetchuck\Robo\Shellcheck\Task\CliCheckFilesTask|\Robo\Collection\CollectionBuilder $task
 */
class CliCheckFilesTaskTest extends TaskTestBase
{
    protected function initTask()
    {
        $this->task = $this->taskBuilder->taskShellcheckCheckFiles();
    }

    public function casesGetCommand(): array
    {
        return [
            'without any option and argument' => [
                "shellcheck --format='json'",
                [],
            ],
            'executable' => [
                "/my/shellcheck/foo --format='json'",
                [
                    'executable' => '/my/shellcheck/foo',
                ],
            ],
            'with env vars' => [
                "FOO1='bar1' FOO2='bar2' shellcheck --format='json'",
                [
                    'envVars' => [
                        'FOO1' => 'bar1',
                        'FOO2' => 'bar2',
                    ],
                ],
            ],
            'workingDirectory with env vars' => [
                "cd '/my/dir01' && FOO1='bar1' FOO2='bar2' shellcheck --format='json'",
                [
                    'workingDirectory' => '/my/dir01',
                    'envVars' => [
                        'FOO1' => 'bar1',
                        'FOO2' => 'bar2',
                    ],
                ],
            ],
            'all in one' => [
                implode(' ', [
                    "cd '/my/dir01' &&",
                    "FOO1='bar1'",
                    'my-shellcheck',
                    '--check-sourced',
                    "--include='a,c'",
                    "--exclude='d,e'",
                    "--format='checkstyle'",
                    '--norc',
                    "--enable='g,h'",
                    "--source-path='/my/source/path01'",
                    "--shell='mysh'",
                    "--wiki-link-count='42'",
                    '--external-sources',
                    "--color='always'",
                    "'./a.bash'",
                    "'./b.bash'",
                ]),
                [
                    'workingDirectory' => '/my/dir01',
                    'envVars' => [
                        'FOO1' => 'bar1',
                    ],
                    'executable' => 'my-shellcheck',
                    'color' => 'always',
                    'checkSourced' => true,
                    'include' => [
                        'a' => true,
                        'b' => false,
                        'c' => true,
                    ],
                    'exclude' => [
                        'd',
                        'e',
                    ],
                    'format' => 'checkstyle',
                    'noRc' => true,
                    'enable' => [
                        'f' => false,
                        'g' => true,
                        'h' => true,
                    ],
                    'sourcePath' => '/my/source/path01',
                    'shell' => 'mysh',
                    'wikiLinkCount' => 42,
                    'externalSources' => true,
                    'files' => [
                        './a.bash',
                        './b.bash',
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
        $jsonResult = [
            [
                'file' => 'a.bash',
                'line' => 1,
                'endLine' => 2,
                'column' => 3,
                'endColumn' => 4,
                'level' => 'error',
                'code' => 5,
                'message' => 'my-message',
                'fix' => null,
            ]
        ];
        $reportData = (new JsonToReport())->convertFromJson(json_encode($jsonResult));

        $expected = [
            'exitCode' => 1,
            'data' => [
                'abc.shellcheck.check.report' => new ReportWrapper($reportData),
            ],
            'log' => [
                [
                    'notice',
                    'runs "<info>{command}</info>"',
                    [
                        'command' => "shellcheck --format='json'",
                        'name' => 'ShellCheck - Check files',
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
            'exitCode' => 1,
            'stdOutput' => json_encode($jsonResult),
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
                $this->tester->assertArrayHasKey($key, $actualData);
                $this->tester->assertEquals(
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
