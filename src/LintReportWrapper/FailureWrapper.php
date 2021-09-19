<?php

declare(strict_types = 1);

namespace Sweetchuck\Robo\Shellcheck\LintReportWrapper;

use Sweetchuck\LintReport\FailureWrapperInterface;

class FailureWrapper implements FailureWrapperInterface
{
    protected array $failure = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $failure)
    {
        // @todo Validate.
        $this->failure = $failure + [
            'file' => '',
            'line' => 0,
            'endLine' => 0,
            'column' => 0,
            'endColumn' => 0,
            'level' => '',
            'code' => '',
            'message' => '',
            'fix' => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function severity(): string
    {
        return strtolower($this->failure['level']);
    }

    /**
     * {@inheritdoc}
     */
    public function source(): string
    {
        return $this->failure['code'];
    }

    /**
     * {@inheritdoc}
     */
    public function line(): int
    {
        return $this->failure['line'];
    }

    /**
     * {@inheritdoc}
     */
    public function column(): int
    {
        return $this->failure['column'];
    }

    /**
     * {@inheritdoc}
     */
    public function message(): string
    {
        return $this->failure['message'];
    }
}
