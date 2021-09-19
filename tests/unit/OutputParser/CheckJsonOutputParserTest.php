<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\Tests\Unit\OutputParser;

use Sweetchuck\Robo\Shellcheck\LintReportWrapper\ReportWrapper;
use Sweetchuck\Robo\Shellcheck\OutputParser\CheckJsonOutputParser;
use Sweetchuck\Robo\Shellcheck\ResultConverter\JsonToReport;

class CheckJsonOutputParserTest extends TestBase
{
    protected string $parserClass = CheckJsonOutputParser::class;

    public function casesParse(): array
    {
        $converter = new JsonToReport();

        return [
            'non-exit-code' => [
                [
                    'exitCode' => 2,
                    'errorMessages' => [
                        'my-error-message-01',
                    ],
                    'assets' => [],
                ],
                'exitCode' => 2,
                'stdOutput' => '',
                'stdError' => 'my-error-message-01',
                [
                    'assetNameMapping' => [
                        'report' => 'shellcheck.check.report',
                    ],
                ],
            ],
            'empty' => [
                [
                    'exitCode' => 0,
                    'errorMessages' => [],
                    'assets' => [
                        'shellcheck.check.report' => new ReportWrapper($converter->convert('[]')),
                    ],
                ],
                'exitCode' => 0,
                'stdOutput' => '[]',
                'stdError' => '',
                [
                    'assetNameMapping' => [
                        'report' => 'shellcheck.check.report',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider casesParse
     */
    public function testParse(
        array $expected,
        int $exitCode,
        string $stdOutput,
        string $stdError,
        array $options
    ) {
        $this->parser->setOptions($options);

        $this->tester->assertEquals(
            $expected,
            $this->parser->parse($exitCode, $stdOutput, $stdError),
        );
    }
}
