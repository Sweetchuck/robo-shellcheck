<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Unit\ResultConverter;

use Codeception\Test\Unit;
use Sweetchuck\Robo\Shellcheck\ResultConverter\JsonToReport;
use Sweetchuck\Robo\Shellcheck\Tests\UnitActor;

/**
 * @covers \Sweetchuck\Robo\Shellcheck\ResultConverter\JsonToReport<extended>
 */
class JsonToReportTest extends Unit
{

    protected UnitActor $tester;

    public function casesConvert(): array
    {
        return [
            'basic' => [
                [
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
                                    'code' => '5',
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
                                    'code' => '5',
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
                                    'code' => '5',
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
                ],
                json_encode([
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
                ]),
            ],
        ];
    }

    /**
     * @dataProvider casesConvert
     */
    public function testConvert(array $expected, string $json)
    {
        $converter = new JsonToReport();

        $this->tester->assertSame(
            $expected,
            $converter->convert($json),
        );
    }
}
