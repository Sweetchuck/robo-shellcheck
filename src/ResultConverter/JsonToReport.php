<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\ResultConverter;

class JsonToReport
{

    public function convertFromJson(string $stdOutput): array
    {
        return $this->convertFromArray(json_decode($stdOutput, true));
    }

    public function convertFromArray(array $failures): array
    {
        $report = [
            'files' => [],
            'errors' => 0,
            'warnings' => 0,
            'fixable' => 0,
        ];
        foreach ($failures as $failure) {
            settype($failure['code'], 'string');

            if (!array_key_exists($failure['file'], $report['files'])) {
                $report['files'][$failure['file']] = [
                    'filePath' => $failure['file'],
                    'failures' => [],
                    'errors' => 0,
                    'warnings' => 0,
                    'fixable' => 0,
                ];
            }

            $report['files'][$failure['file']]['failures'][] = $failure;
            $report['files'][$failure['file']]['errors'] += $failure['level'] === 'error' ? 1 : 0;
            $report['files'][$failure['file']]['warnings'] += $failure['level'] === 'warning' ? 1 : 0;
            $report['files'][$failure['file']]['fixable'] += $failure['fix'] !== null ? 1 : 0;
        }

        foreach ($report['files'] as $file) {
            $report['errors'] += $file['errors'];
            $report['warnings'] += $file['warnings'];
            $report['fixable'] += $file['fixable'];
        }

        return $report;
    }
}
