<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Unit\LintReportWrapper;

use Codeception\Test\Unit;
use Sweetchuck\LintReport\ReportWrapperInterface;
use Sweetchuck\Robo\Shellcheck\LintReportWrapper\ReportWrapper;
use Sweetchuck\Robo\Shellcheck\ResultConverter\JsonToReport;
use Sweetchuck\Robo\Shellcheck\Tests\UnitActor;

/**
 * @covers \Sweetchuck\Robo\Shellcheck\LintReportWrapper\ReportWrapper<extended>
 * @covers \Sweetchuck\Robo\Shellcheck\LintReportWrapper\FileWrapper<extended>
 * @covers \Sweetchuck\Robo\Shellcheck\LintReportWrapper\FailureWrapper<extended>
 */
class ReportWrapperTest extends Unit
{

    protected UnitActor $tester;

    public function testCreate()
    {
        $reportData = [
            'files' => [
                'a.bash' => [
                    'filePath' => 'a.bash',
                    'failures' => [
                        [
                            'file' => 'a.bash',
                            'line' => 1,
                            'endLine' => 2,
                            'column' => 3,
                            'endColumn' => 4,
                            'level' => 'warning',
                            'code' => 5,
                            'message' => '',
                            'fix' => null,
                        ],
                        [
                            'file' => 'a.bash',
                            'line' => 6,
                            'endLine' => 7,
                            'column' => 8,
                            'endColumn' => 9,
                            'level' => 'error',
                            'code' => 5,
                            'message' => '',
                            'fix' => null,
                        ],
                    ],
                    'errors' => 1,
                    'warnings' => 1,
                    'fixable' => 0,
                ],
                'b.bash' => [
                    'filePath' => 'b.bash',
                    'failures' => [
                        [
                            'file' => 'b.bash',
                            'line' => 6,
                            'endLine' => 7,
                            'column' => 8,
                            'endColumn' => 9,
                            'level' => 'error',
                            'code' => 5,
                            'message' => '',
                            'fix' => null,
                        ],
                    ],
                    'errors' => 1,
                    'warnings' => 0,
                    'fixable' => 0,
                ],
            ],
            'errors' => 2,
            'warnings' => 1,
            'fixable' => 0,
        ];

        $report = new ReportWrapper($reportData);
        $this->tester->assertSame($reportData, $report->getReport());

        $this->tester->assertSame(2, $report->countFiles());
        $this->tester->assertSame(2, $report->numOfErrors());
        $this->tester->assertSame(1, $report->numOfWarnings());
        $this->tester->assertSame(ReportWrapperInterface::SEVERITY_ERROR, $report->highestSeverity());

        foreach ($report->yieldFiles() as $filePath => $file) {
            $expectedFile = $reportData['files'][$filePath];
            $this->tester->assertSame($expectedFile['errors'], $file->numOfErrors());
            $this->tester->assertSame($expectedFile['warnings'], $file->numOfWarnings());
        }
    }

    public function casesHighestSeverity(): array
    {
        return [
            'empty' => ['ok', []],
            'warning' => [
                'warning',
                [
                    [
                        'file' => 'a.bash',
                        'level' => 'warning',
                        'code' => 42,
                        'severity' => 42,
                        'fix' => null,
                    ],
                ],
            ],
            'error' => [
                'error',
                [
                    [
                        'file' => 'a.bash',
                        'level' => 'warning',
                        'code' => 42,
                        'severity' => 42,
                        'fix' => null,
                    ],
                    [
                        'file' => 'a.bash',
                        'level' => 'error',
                        'code' => 42,
                        'severity' => 42,
                        'fix' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesHighestSeverity
     */
    public function testHighestSeverity(string $expected, array $result)
    {
        $reportData = (new JsonToReport())->convertFromArray($result);
        $this->tester->assertSame(
            $expected,
            (new ReportWrapper($reportData))->highestSeverity(),
        );
    }
}
