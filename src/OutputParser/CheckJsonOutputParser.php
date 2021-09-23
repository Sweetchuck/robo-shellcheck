<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\OutputParser;

use Sweetchuck\Robo\Shellcheck\LintReportWrapper\ReportWrapper;
use Sweetchuck\Robo\Shellcheck\ResultConverter\JsonToReport;

class CheckJsonOutputParser extends OutputParserBase
{

    protected JsonToReport $converter;

    public function __construct(?JsonToReport $converter = null)
    {
        $this->converter = $converter ?: new JsonToReport();
    }

    public function parse(int $exitCode, string $stdOutput, string $stdError): array
    {
        $return = [
            'exitCode' => $exitCode,
            'errorMessages' => [],
            'assets' => [],
        ];

        if ($exitCode > 1) {
            if ($stdError) {
                $return['errorMessages'][] = $stdError;
            }

            return $return;
        }

        $key = $this->getExternalAssetName('report');
        $reportData = $this->converter->convertFromJson($stdOutput);
        $return['assets'][$key] = new ReportWrapper($reportData);

        return $return;
    }
}
