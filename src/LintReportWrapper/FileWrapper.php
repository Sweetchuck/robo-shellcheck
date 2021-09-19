<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\LintReportWrapper;

use Sweetchuck\LintReport\FileWrapperInterface;
use Sweetchuck\LintReport\ReportWrapperInterface;

class FileWrapper implements FileWrapperInterface
{
    protected array $file = [];

    public array $stats = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $file)
    {
        $this->file = $file + [
            'filePath' => '',
            'errors' => '',
            'warnings' => '',
            'fixable' => '',
            'failures' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function filePath(): string
    {
        return $this->file['filePath'];
    }

    /**
     * {@inheritdoc}
     */
    public function numOfErrors(): int
    {
        return $this->file['errors'];
    }

    /**
     * {@inheritdoc}
     */
    public function numOfWarnings(): int
    {
        return $this->file['warnings'];
    }

    /**
     * {@inheritdoc}
     */
    public function yieldFailures()
    {
        foreach ($this->file['failures'] as $failure) {
            yield new FailureWrapper($failure);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stats(): array
    {
        if (!$this->stats) {
            $this->stats = [
                'severityWeight' => 0,
                'severity' => '',
                'has' => [
                    ReportWrapperInterface::SEVERITY_OK => false,
                    ReportWrapperInterface::SEVERITY_WARNING => false,
                    ReportWrapperInterface::SEVERITY_ERROR => false,
                ],
                'source' => [],
            ];
            foreach ($this->file['failures'] as $failure) {
                $severity = strtolower($failure['level']);
                $severityWeight = $this->severityWeight($severity);
                if ($this->stats['severityWeight'] < $severityWeight) {
                    $this->stats['severityWeight'] = $severityWeight;
                    $this->stats['severity'] = $severity;
                }

                $this->stats['has'][$severity] = true;

                $this->stats['source'] += [
                    $failure['code'] => [
                        'severity' => $severity,
                        'count' => 0,
                    ],
                ];
                $this->stats['source'][$failure['code']]['count']++;
            }
        }

        return $this->stats;
    }

    /**
     * {@inheritdoc}
     */
    public function highestSeverity(): string
    {
        if ($this->numOfErrors()) {
            return ReportWrapperInterface::SEVERITY_ERROR;
        }

        if ($this->numOfWarnings()) {
            return ReportWrapperInterface::SEVERITY_WARNING;
        }

        return ReportWrapperInterface::SEVERITY_OK;
    }

    protected function severityWeight(string $level): int
    {
        $mapping = [
            'error' => 2,
            'warning' => 1,
            'ok' => 0,
            '' => 0,
        ];

        return $mapping[$level];
    }
}
